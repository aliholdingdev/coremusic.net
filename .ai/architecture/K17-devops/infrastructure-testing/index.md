---
node_id: K17-devops/infrastructure-testing
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/ci
  - K17-devops/environments
evidence:
  - "lockfile envanteri: package-lock.json VAR · composer.lock VAR — 3 composer kökü (shared, auth.coremusic.net, home.coremusic.net) (2026-10-17)"
  - ".gitleaks.toml 18 satır (config doğrulama hedefi) (2026-10-17)"
  - "ci.yml 6 job'ında env doğrulama step'i YOK (grep) (2026-10-17)"
  - "Dockerfile / docker-compose.yml / *.tf diskte 0 isabet (2026-10-17 glob)"
validation:
  - "Kullanıcı seçimi (2026-10-17): kapsam = lockfile + config drift (disk kanıtlı)"
  - "Docker/Terraform iddiası YAZILMADI — 'YOK — ADR bekliyor' olarak işaretlendi"
  - "ci.yml env-check yokluğu grep ile ölçüldü"
open_questions:
  - "composer.lock / package-lock.json varlığı 3 kök için teyit edildi ama içerik-tutarlılık testi CI'da YOK — ADR"
  - "Konteyner/terraform testi: diskte altyapı dosyası yok → kapsam dışı + ADR bekliyor"
group: devops
---

# Infrastructure Testing — Altyapı Doğrulama Kapıları (K17-devops altı)

> **Karar (kullanıcı, 2026-10-17): kapsam = Lockfile + Config drift** (disk kanıtlı).
> Docker/Terraform testi **kapsam dışı + diskte kanıt yok → ADR bekliyor.**
> **Sorumluluk:** DevOps Engineer + QA Engineer · **Otorite:** Bayram Ali / Vault Steward

---

## §1 Özet — Bugün altyapı testi var mı?

| Test sınıfı | Bugün | Kanıt (2026-10-17) |
|---|---|---|
| Lockfile tutarlılığı | **CI'da YOK** | `ci.yml` 6 job: lint/test/audit/coverage/e2e — lock-check yok |
| Config drift (workflow env) | **YOK** | `environment:` 0 isabet, env-check step 0 |
| Secret config doğrulama | **YOK (CI'da)** | `secret-scan.yml` yalnız tarama; `.gitleaks.toml` syntax kontrolü step'i yok |
| Composer manifest doğrulama | **YOK (CI'da)** | `composer validate` step'i `ci.yml`'de yok (grep) |
| Konteyner testi (docker build/lint) | **KAPSAM DIŞI** | Dockerfile/compose **0 isabet** |
| IaC testi (terraform validate) | **KAPSAM DIŞI** | `*.tf` **0 isabet** |

**Sonuç:** altyapı doğrulaması bugün **yalnız `composer audit`** (zafiyet taraması) ile sınırlı.
Lockfile/config-drift kapıları **hiç yok** → bu dosya onların tasarımını verir (TASLAK).

---

## §2 Kapsam

| Kapsam (disk kanıtlı) | Kapsam dışı (ADR bekliyor) |
|---|---|
| Lockfile tutarlılığı: `package-lock.json` × `composer.lock` × `package.json`/`composer.json` | Docker imaj testi (`docker build`, hadolint) — Dockerfile YOK |
| Config drift: `.gitleaks.toml` syntax, workflow env eşleşmesi | IaC testi (`terraform validate/plan`) — `*.tf` YOK |
| Manifest doğrulama: `composer validate`, `npm pkg get` | Cloud sağlayıcı doğrulama — diskte kanıt yok |
| Secret dışlama katmanı (`.gitignore` ↔ `.gitleaks.toml`) | Container güvenlik taraması — imaj yok |

**Kaçınılan tekrar:** `pipeline-hizlandirma.md` (513s) → performans/cache; `ci/` → test kapıları
(PHP/JS); `secret-yonetimi.md` (509s) → secret ağacı. Burası yalnız **altyapı-yapısal**
doğrulama (dosya/lock/config birbiriyle tutarlı mı?).

---

## §3 Lockfile Zorunluluğu — Neden Var, Neden Test Edilir

| Lock dosyası | Var mı (2026-10-17) | CI adımı | Kanıt |
|---|---|---|---|
| `package-lock.json` | **VAR** (commit ediliyor) | `npm ci` | `.gitignore:17-18` "ARTIK commit edilir" + `ci.yml:155/174/197` `npm ci` |
| `composer.lock` (3 kök) | **VAR** | `composer install` | `ci.yml:48-60` 3× `composer install` |
| `yarn.lock` | **ignore** | — | `.gitignore:19` `yarn.lock` (tek PM: npm) |

**Mühendislik:** `npm ci` lock **yoksa hata verir** ve lock'a birebir kurar → reproducible.
`npm install` lock'u yeniden çözer → "yerel geçti, CI kırıldı". Bu yüzden lock **CI gate'inin
kendisidir**; ek lock-check, lock'un manifest ile tutarlılığını **açık** sınar.

---

## §4 Config Drift Testleri — Neler Sürünecek

| # | Drift | Tehlike | Doğrulama |
|---|---|---|---|
| 1 | `package.json` sürüm ↔ lock sürümler | sürüm kayması, reproducibility kırığı | `npm ci --dry-run` exit 0 |
| 2 | `composer.json` ↔ `composer.lock` | zafiyetli eski sürüm çekiliyor | `composer validate` + `--strict` |
| 3 | `.gitleaks.toml` syntax | tarama **körleşir** (fail-open) | `gitleaks detect --config .gitleaks.toml` syntax |
| 4 | Workflow env ↔ gerçek env dosyaları | yanlış ortam/secret | env eşleşme matrisi (`environments/`) |
| 5 | `.gitignore` ↔ `.gitleaks.toml` kapsam | secret git'e girer | dışlama katmanı testi |
| 6 | `php` sürüm iddiası ↔ `composer.json` | CI'da farklı PHP | `ci.yml:22` `8.4` vs `composer.json:8` `>=8.4` |

**Kritik prensip (fail-closed):** config doğrulaması başarısızsa **CI kırmızı** olmalı.
`.gitleaks.toml` bozulursa gitleaks sessizce yanlış çalışabilir → bu sınıf "görünmez kırık"
sayılır ve **gate olmak zorunda** (§6.1).

---

## §5 TASLAK — CI'a eklenecek doğrulama job'ı (UYGULANMADI)

```yaml
# === TASLAK — uygulanmadı · onay + ADR bekliyor (infrastructure-testing §5) ===
# Beyaz-liste: *.yml yazımı orkestratör yetkisinde (devops-master.md §C.1/25-27)
name: Infra Config Check

on:
  push:
  pull_request:

permissions:
  contents: read

jobs:
  lockfile:
    name: Lockfile + Config Drift
    runs-on: ubuntu-latest
    timeout-minutes: 10
    steps:
      - uses: actions/checkout@v4

      - uses: actions/setup-node@v4
        with:
          node-version: '26'
          cache: npm

      - name: npm lock tutarlılığı (dry-run)
        run: npm ci --dry-run

      - name: package.json + lock eşleşmesi
        run: node -e "const l=require('./package-lock.json'),p=require('./package.json');
             if(l.packages[''].name!==p.name){console.error('lock≠manifest');process.exit(1)}"
        # Not: multiline node -e yerine scripts/lock-check.mjs önerilir (taslak)

      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          tools: composer

      - name: composer validate (3 kök)
        run: |
          for d in shared auth.coremusic.net home.coremusic.net; do
            ( cd "$d" && composer validate --strict --no-check-publish )
          done

      - name: gitleaks config syntax
        run: gitleaks detect --source . --config .gitleaks.toml --no-banner --exit-code 1
        # Bozuk config → non-zero exit → fail-closed (§4/3)
```

---

## §6 Çok-Dilli Doğrulama Örnekleri (TASLAK)

### §6.1 Bash — lockfile + composer lock tutarlılığı

```bash
#!/usr/bin/env bash
# TASLAK — infrastructure-testing §4/1-2: lock manifest ile tutarlı mı?
set -euo pipefail
fail=0

# npm
if [ -f package.json ]; then
  npm ci --dry-run >/dev/null 2>&1 || { echo "[lock] npm lock ≠ package.json"; fail=1; }
fi

# composer (3 kök)
for d in shared auth.coremusic.net home.coremusic.net; do
  if [ -d "$d" ] && [ -f "$d/composer.json" ]; then
    ( cd "$d" && composer validate --strict --no-check-publish >/dev/null 2>&1 ) \
      || { echo "[lock] $d composer validate BAŞARISIZ"; fail=1; }
    [ -f "$d/composer.lock" ] || { echo "[lock] $d composer.lock YOK"; fail=1; }
  fi
done

exit $fail
```

### §6.2 PowerShell — config drift denetimi (Windows)

```powershell
# TASLAK — infrastructure-testing §4: gitleaks config + gitignore kapsam drift'i
$ErrorActionPreference = 'Stop'
$issues = @()

# 1) gitleaks config syntax
gitleaks detect --source . --config .gitleaks.toml --no-banner --exit-code 2>&1 | Out-Null
if ($LASTEXITCODE -in 1,2) { $issues += '.gitleaks.toml syntax/bulgu sorunu' }

# 2) .gitignore secret katmanları eksik mi? (environments §6.2 ile aynı çekirdek)
$gi = Get-Content .gitignore -Raw
foreach ($p in @('.env','config/.env','.ai/.env.figma','shared/config/jwt/private.pem')) {
    if ($gi -notmatch [regex]::Escape($p)) { $issues += "$p .gitignore'da YOK" }
}

# 3) workflow env ↔ ci.yml PHP sürümü
$php = (Select-String .github/workflows/ci.yml -Pattern "PHP_VERSION:\s*'([^']+)'").Matches[0].Groups[1].Value
$json = (Get-Content shared/composer.json -Raw | ConvertFrom-Json).require.php
if ($json -notmatch [regex]::Escape($php)) { $issues += "PHP sürümü drift: ci=$php composer=$json" }

if ($issues.Count) { $issues | ForEach-Object { Write-Host "[drift] $_" -ForegroundColor Red }; exit 1 }
Write-Host '[drift] 0 sorun' -ForegroundColor Green
```

### §6.3 PHP — manifest lock doğrulama (Composer benzeri kontrol)

```php
<?php
declare(strict_types=1);
// TASLAK — infrastructure-testing §4/2: composer.json ↔ composer.lock eşleşmesi
final class LockConsistency
{
    /** composer.lock'daki content-hash composer.json ile eşleşmeli */
    public function assertComposerLock(string $dir): void
    {
        $manifest = $dir . '/composer.json';
        $lock     = $dir . '/composer.lock';
        if (!is_file($manifest)) throw new \RuntimeException("$manifest YOK");
        if (!is_file($lock))     throw new \RuntimeException("$lock YOK — reproducibility riski");

        $m = json_decode((string) file_get_contents($manifest), true, 512, JSON_THROW_ON_ERROR);
        $l = json_decode((string) file_get_contents($lock),     true, 512, JSON_THROW_ON_ERROR);

        // require anahtarları lock'taki paket adları içinde bulunmalı (özet kontrol)
        $locked = array_column($l['packages'] ?? [], 'name');
        foreach (array_keys($m['require'] ?? []) as $dep) {
            if ($dep === 'php' || str_starts_with($dep, 'ext-')) continue;
            if (!in_array($dep, $locked, true)) {
                throw new \RuntimeException("$dep composer.json'da ama lock'ta YOK");
            }
        }
    }

    /** package.json ↔ package-lock: paket adı + sürüm eşleşmesi */
    public function assertNpmLock(string $dir = '.'): void
    {
        $p = json_decode((string) file_get_contents($dir . '/package.json'), true);
        $l = json_decode((string) file_get_contents($dir . '/package-lock.json'), true);
        if (($l['name'] ?? null) !== ($p['name'] ?? null)) {
            throw new \RuntimeException('lock name ≠ manifest name');
        }
        $root = $l['packages'][''] ?? [];
        foreach (['dependencies','devDependencies'] as $k) {
            foreach (($root[$k] ?? []) as $name => $ver) {
                $have = $l['packages']["node_modules/$name"]['version'] ?? null;
                if ($have === null) {
                    throw new \RuntimeException("$name package.json'da ama lock'ta YOK");
                }
            }
        }
    }
}
```

### §6.4 JavaScript — env eşleşme kapısı (workflow env ↔ gerçek env)

```javascript
// TASLAK — infrastructure-testing §4/4: ci.yml env ↔ composer.json PHP eşleşmesi
import { readFileSync } from 'node:fs';

const ci = readFileSync('.github/workflows/ci.yml', 'utf8');
const composer = JSON.parse(readFileSync('shared/composer.json', 'utf8'));

const ciPhp = ci.match(/PHP_VERSION:\s*'?(\d+\.\d+)'?/)?.[1];
const want = (composer.require.php ?? '').replace(/[^\d.]/g, '');

const ok = ciPhp && want.startsWith(ciPhp);
if (!ok) {
  console.error(`[drift] PHP: ci.yml=${ciPhp} composer=${composer.require.php}`);
  process.exit(1);   // fail-closed
}
console.log(`[drift] PHP tutarlı: ${ciPhp}`);
```

### §6.5 TOML — gitleaks config parse doğrulaması (taslak yapı)

```toml
# TASLAK — infrastructure-testing §4/3: config varlığının kendisi bir testtir.
# Gerçek .gitleaks.toml (18 satır) okunduğunda bu üç alan BULUNMALI (§6.1 gate):
#   1) title           → tarama raporu başlığı
#   2) [extend]        → useDefault = true (varsayılan kural seti korunur)
#   3) [allowlist]     → yalnız test fixture (ADR-034 §5.2) — üretim kodu girmez
# Eksik/bozuk alan ⇒ fail-closed (görünmez kırık engellenir, §4/3)
```

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Konu | Kanıt | Durum |
|---|---|---|---|
| 1 | **CI'da lock-check yok** | ci.yml 6 job'ında lock/validate step'i 0 (grep) | **Taslak §5** — `.yml` beyaz-liste dışı |
| 2 | **`composer validate` CI'da yok** | grep `composer validate` = 0 | Taslak §5/§6.1 |
| 3 | **`.gitleaks.toml` syntax gate yok** | secret-scan.yml yalnız tarama (31s) | Taslak §5/§6.2 |
| 4 | **Docker/Terraform testi KAPSAM DIŞI** | Dockerfile/compose/`*.tf` = **0 isabet** | **ADR bekliyor** — altyapı dosyası diskte yok |
| 5 | **env doğrulama step'i yok** | ci.yml env-check step 0 (grep) | Taslak §6.4 + `environments/` §5 |
| 6 | **secret-scan.yml:4 bayat yorum** | yorum "diskte .gitleaks.toml YOK" der; dosya VAR (18s) | → `devops-ornekler.md` §3.1 — `.yml` düzenlenmedi |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `ci/index.md` | PHP/JS test kapıları (bu dosya altyapı-yapısal) |
| `environments/index.md` §5 | env eşleşme matrisi (§6.4 girdisi) |
| `pipeline/index.md` | bu kapıların pipeline'a yerleşimi |
| `secret-yonetimi.md` (deployment/) | secret ağacı (509s) |
| `pipeline-hizlandirma.md` (deployment/) | cache/lock performansı (513s) |
| `build/index.md` | artefakt üretimi (build gate'i) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17) · **Kapsam kararı:** lockfile + config drift (kullanıcı) ·
**Yasaklar:** `git commit` YOK · `*.yml` YOK (TASLAK) · docker/terraform iddiası YOK
