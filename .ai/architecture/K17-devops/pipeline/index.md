---
node_id: K17-devops/pipeline
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/ci
  - K17-devops/cd
  - K17-devops/build
  - K17-devops/infrastructure-testing
evidence:
  - "pipeline/cicd-asamalari.md · 511 satır (2026-10-17 envanter ölçümü)"
  - "pipeline/pipeline-hizlandirma-ve-cache.md · 513 satır (2026-10-17 envanter ölçümü)"
  - ".github/workflows/ci.yml · 203 satır · 6 job (2026-10-17 tam okuma)"
  - ".github/workflows/secret-scan.yml · 31 satır · 1 job (2026-10-17 tam okuma)"
validation:
  - "index.md YOKTU → bu sürüm eklendi (iskelet + indeks + gap analizi)"
  - "İçerik dosyaları TEKRAR EDİLMEZ (rollback yaklaşımı): indeks + link + gap"
  - "ci.yml job sayısı: php-lint·php-test·composer-audit·js-test·js-coverage·js-e2e = 6"
open_questions:
  - "pipeline-hizlandirma-ve-cache.md içeriği OKUNMADI → §6 gap"
  - "cicd-asamalari.md içeriği OKUNMADI → §6 gap"
group: devops
---

# Pipeline — CI/CD Şerit Genel Bakışı ve İndeks (K17-devops altı)

> **TEKRAR YASAĞI:** `pipeline/cicd-asamalari.md` (511s) + `pipeline-hizlandirma-ve-cache.md`
> (513s) içerik dosyalarıdır; bu `index.md` onları **tekrarlamaz**. Rolü: şerit haritası,
> job ↔ alan eşlemesi, gap analizi, link. **Sorumluluk:** DevOps Engineer + QA Engineer ·
> **Otorite:** Bayram Ali / Vault Steward · **Mod:** Red Team · Human Mode · Truth Mode

---

## §1 Özet — Bugün şerit nasıl akıyor?

| Şerit | Durum | Kanıt (2026-10-17) |
|---|---|---|
| **CI** (doğrulama) | **VAR** — `ci.yml` 203s, 6 job | ci.yml tam okuma |
| **Secret scan** (tarama) | **VAR** — `secret-scan.yml` 31s, 1 job | secret-scan.yml tam okuma |
| **CD** (dağıtım) | **YOK** — deploy job 0 | grep `environment:`/`deploy` = 0 |
| **Build artefakt** | **VAR** (JS: vitest) + **parçalı** (PHP: composer) | ci.yml:48-60 3× composer · ci.yml:155 npm ci |
| **Cache/perf** | **VAR** — composer cache + npm cache | ci.yml:40-44 · 153 |
| **Pipeline hızlandırma** | **VAR** (concurrency + cache) + içerik dosyası | ci.yml:17-19 · 513s dosya |

**Sonuç:** şerit **yalnız CI tarafında** tam. CD, artefakt-publish ve rollback tetikleyicisi
eksik → bu dosya bu boşlukları işaretler (§6).

---

## §2 Şerit Haritası — Job ↔ Alan Eşlemesi

```
push / pull_request
        │
        ├──► ci.yml (203s) ─ 6 job ─────────────────────────────────┐
        │      ├── php-lint      (statik analiz + composer install)  │
        │      ├── php-test      (matrix: shared, auth — needs:lint) │
        │      ├── composer-audit (zafiyet taraması, 3 kök)          │
        │      ├── js-test       (vitest)                            │
        │      ├── js-coverage   (vitest --coverage, soft A7)        │
        │      └── js-e2e        (playwright, soft A6)               │
        │                                                            │
        └──► secret-scan.yml (31s) ─ 1 job ──────────────────────────┤
               └── gitleaks    (fetch-depth: 0, tam geçmiş)          │
                                                                     ▼
                                                          [CI GREEN] ──► CD (YOK — TASLAK cd/)
```

### §2.1 Job envanteri (ci.yml — birebir kanıt)

| # | Job | `needs` | Soft? | Alan |
|---|---|---|---|---|
| 1 | `php-lint` | — | hayır | PHP statik analiz + composer install |
| 2 | `php-test` | php-lint | hayır | PHPUnit (matrix: shared, auth) |
| 3 | `composer-audit` | — | hayır | Composer zafiyet taraması |
| 4 | `js-test` | — | hayır | Vitest unit |
| 5 | `js-coverage` | js-test | **evet** (A7) | Vitest coverage |
| 6 | `js-e2e` | — | **evet** (A6) | Playwright e2e |

**Toplam 6 job · 2 soft · 2 `needs` bağımlılığı.** Ayrıntı → `ci/index.md` + `devops-ornekler.md` §2.

### §2.2 Secret scan job (secret-scan.yml — birebir kanıt)

| Job | `fetch-depth` | Trigger |
|---|---|---|
| `gitleaks` | **0** (tam geçmiş) | `push:` + `pull_request:` (schedule YOK) |

---

## §3 Hızlandırma Sinyalleri (cache + concurrency — ci.yml kanıtı)

| Hızlandırma | Konum | Ne yapar |
|---|---|---|
| `concurrency` + `cancel-in-progress` | ci.yml:17-19 | Aynı ref'de eskisini iptal |
| Composer cache | ci.yml:40-44 | `~/.cache/composer` — lock hash'ine bağlı |
| npm cache | ci.yml:153 · 173 · 196 | `cache: npm` (setup-node yerleşik) |
| `--prefer-dist` | ci.yml:49-60 | Kaynak yerine dağıtım (dist) indirme |

**Ayrıntı** → `pipeline-hizlandirma-ve-cache.md` (513s) — **bu dosyada tekrar EDİLMEDİ**.

---

## §4 Kapsam ve Kapsam Dışı

| Kapsam | Kapsam Dışı |
|---|---|
| Şerit haritası + job eşlemesi | Job içeriği adım adım → `ci/index.md` + `devops-ornekler.md` §2 |
| Pipeline ↔ alan linkleri | Deploy mekaniği → `cd/index.md` |
| Hızlandırma *sinyalleri* (özet) | Hızlandırma *detayı* → `pipeline-hizlandirma-ve-cache.md` (513s) |
| CI/CD aşamaları *haritası* | Aşama *detayı* → `pipeline/cicd-asamalari.md` (511s) |
| Gap analizi | — |

**Kaçınılan tekrar:** `cicd-asamalari.md` (511s) + `pipeline-hizlandirma-ve-cache.md` (513s)
**okunmadı** → içerikleri hakkında iddia `⚠️ VERIFICATION REQUIRED` olurdu (§6).

---

## §5 Gap Analizi (inden eksikler)

| # | Gap | Tip | Kanıt | Aksiyon |
|---|---|---|---|---|
| 1 | `pipeline/index.md` **YOKTU** | yapısal | envanter: 0 dosya | → bu sürüm 1.0.0 (dolduruldu) |
| 2 | `cicd-asamalari.md` (511s) **okunmadı** | bilgi | Read YOK | **`⚠️ VERIFICATION REQUIRED`** |
| 3 | `pipeline-hizlandirma-ve-cache.md` (513s) **okunmadı** | bilgi | Read YOK | **`⚠️ VERIFICATION REQUIRED`** |
| 4 | CD job'ı yok | tasarım | grep deploy = 0 | → `cd/index.md` §6 TASLAK |
| 5 | Artefakt-publish yok (`upload-artifact` ci.yml'de 0) | tasarım | grep upload-artifact = 0 | → `build/index.md` TASLAK |
| 6 | Pipeline hızlandırma detayı içerik dosyasında (okunmadı) | bilgi | 513s | Doğrulama: Read 513s |
| 7 | CI run geçmişi diskte değil | kanıt | GitHub arayüzü | `⚠️ VERIFICATION REQUIRED` |

---

## §6 Çok-Dilli — Pipeline TASLAK Örnekleri (UYGULANMADI)

> `.yml` beyaz-liste dışı (`devops-master.md` §C.1/25-27). Yalnız şerit haritasını
> somutlaştırmak için.

### §6.1 YAML — bileşik şerit (CI + CD tek dosya, TASLAK)

```yaml
# === TASLAK — uygulanmadı · onay + ADR bekliyor ===
# pipeline/index.md §2 şerit haritasının tek workflow karşılığı
name: Full Pipeline
on:
  push:
    branches: [ main ]
  pull_request:

permissions:
  contents: read

concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: ${{ github.event_name == 'pull_request' }}   # PR'da iptal OK, main'de YOK

jobs:
  ci-validate:            # §2.1 6 job'ın özeti (TASLAK)
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.4', tools: composer }
      - uses: actions/setup-node@v4
        with: { node-version: '26', cache: npm }
      - run: composer install --no-progress --prefer-dist --working-directory shared
      - run: npm ci
      - run: npm run test:js

  secret-scan:            # §2.2
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
        with: { fetch-depth: 0 }
      - uses: gitleaks/gitleaks-action@v2
        env: { GITHUB_TOKEN: "${{ secrets.GITHUB_TOKEN }}" }
```

### §6.2 Bash — pipeline gate özeti (CI → CD kapısı)

```bash
#!/usr/bin/env bash
# TASLAK — pipeline/index.md §5/4: CI green mi, CD açılabilir mi?
set -euo pipefail

# 1) CI kapılarının tamamı geçti mi? (job sayısı = 6 + 1 secret = 7)
ci_status=$(gh run list --workflow ci.yml --limit 1 --json conclusion --jq '.[0].conclusion')
secret_status=$(gh run list --workflow secret-scan.yml --limit 1 --json conclusion --jq '.[0].conclusion')

if [ "$ci_status" != "success" ] || [ "$secret_status" != "success" ]; then
  echo "[pipeline] CI/secret GREEN değil → CD kapalı" >&2; exit 1
fi

echo "[pipeline] CI GREEN → CD adayı"
```

### §6.3 PowerShell — pipeline metriği (job süreleri)

```powershell
# TASLAK — pipeline/index.md §3: job süreleri (hızlandırma ölçümü)
$ErrorActionPreference = 'Stop'
$runs = gh run list --workflow ci.yml --limit 5 --json headSha,conclusion,updatedAt
$runs | ForEach-Object { [pscustomobject]@{
    sha = $_.headSha.Substring(0,7); sonuc = $_.conclusion; zaman = $_.updatedAt
}} | Format-Table -AutoSize

# Amaç: hangi job en yavaş? (§3 hızlandırma sinyallerinin etkisi)
Write-Host '[pipeline] job süreleri gh run view --job <id> ile derinleştirilir'
```

### §6.4 PHP — pipeline durum sınıfı (TASLAK)

```php
<?php
declare(strict_types=1);
// TASLAK — pipeline/index.md §5/4: CI→CD geçiş kapısı
final class PipelineGate
{
    private const REQUIRED = ['php-lint','php-test','composer-audit','js-test'];  // zorunlu
    private const SOFT     = ['js-coverage','js-e2e'];                            // soft (A6/A7)

    /** Zorunlu job'lar success olmalı; soft job'lar yalnız raporlanır */
    public function canPromote(array $jobs): bool
    {
        foreach (self::REQUIRED as $j) {
            if (($jobs[$j] ?? null) !== 'success') return false;   // fail-closed
        }
        return true;
    }

    public function report(array $jobs): array
    {
        return [
            'required' => array_filter(self::REQUIRED, fn ($j) => ($jobs[$j] ?? null) === 'success'),
            'soft'     => array_map(fn ($j) => $jobs[$j] ?? 'unknown', self::SOFT),
        ];
    }
}
```

### §6.5 JavaScript — pipeline gate (Node)

```javascript
// TASLAK — pipeline/index.md §5/4
const REQUIRED = ['php-lint','php-test','composer-audit','js-test'];
const SOFT = ['js-coverage','js-e2e'];

export function canPromote(jobs) {
  return REQUIRED.every(j => jobs[j] === 'success');   // fail-closed
}

export function report(jobs) {
  return {
    required: REQUIRED.filter(j => jobs[j] === 'success'),
    soft: Object.fromEntries(SOFT.map(j => [j, jobs[j] ?? 'unknown'])),
  };
}
```

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Konu | Kanıt | Durum |
|---|---|---|---|
| 1 | **pipeline/index.md YOKTU** | envanter: 0 dosya | → bu sürüm dolduruldu |
| 2 | **CD job yok vs şerit haritası** | grep deploy = 0 | → `cd/index.md` TASLAK |
| 3 | **upload-artifact 0** | grep = 0 | → `build/index.md` TASLAK |
| 4 | **İçerik dosyaları okunmadı** | cicd-asamalari (511s) + pipeline-hizlandirma (513s) | `⚠️ VERIFICATION REQUIRED` |
| 5 | **CI run geçmişi diskte değil** | GitHub arayüzü | `⚠️ VERIFICATION REQUIRED` |
| 6 | **secret-scan.yml:4 bayat yorum** | yorum "diskte .gitleaks.toml YOK" der; dosya VAR (18s) | → `devops-ornekler.md` §3.1 |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `ci/index.md` + `devops-ornekler.md` §2 | Job içeriği adım adım (bu dosya harita) |
| `cd/index.md` §6 | CD job TASLAK'ı (şeridin devamı) |
| `build/index.md` | Artefakt-publish TASLAK'ı (§5/5) |
| `infrastructure-testing/index.md` §5 | Lockfile/config-drift job TASLAK'ı |
| `pipeline/cicd-asamalari.md` (511s) | Aşama detayı (TEKRAR YASAK) |
| `pipeline/pipeline-hizlandirma-ve-cache.md` (513s) | Hızlandırma detayı (TEKRAR YASAK) |
| `rollback/index.md` §6 | CD başarısızsa geri alma |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17 · yeni dosya — iskelet YOKTU) ·
**Yasaklar:** `git commit` YOK · `*.yml` YOK (TASLAK) · içerik dosyaları DOKUNULMAZ (tekrar yok)
