---
title: "K17-devops — Kod Örnekleri"
node_id: K17-devops/devops-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - ".github/workflows/ci.yml:14/17/62/104/151/157/167/175/183/202 · 2026-10-18"
  - ".github/workflows/secret-scan.yml:11-12/14-16/26 · 2026-10-18"
  - ".gitleaks.toml:1-18 · 2026-10-18"
  - ".workflows/deployment.md:126-199 (8 aşama) · :212 (rollback) · :260 (insan onayı) · 2026-10-18"
validation: "2026-10-18 — YAML/TOML/PowerShell blokları diskten; diğer diller iskele (şablon)"
open_questions: "⚠️ iskelelerin production karşılığı UNKNOWN · ⚠️ CI'ın geçtiği doğrulanmadı"
group: kod
---

# K17-devops — Kod Örnekleri

## §1 ELI15

Bu dosya DevOps'un **kod örneklerini** gösterir. Asıl kod (YAML pipeline
kuralları, TOML tarama izinleri, deployment kılavuzu) diskten gelir. Diğer diller
bu katmanın diller arası iskeleleridir: "sürüm notu üret, sağılık bekle, kapıda
dur" kuralları o dilde nasıl görünür. İskele üretim kodu değildir (⚠️ §16).

## §2 Kural

- Her blok dil etiketli, yorumlu (15–60 satır).
- YAML/TOML/PowerShell = disk kanıtı. Diğerleri = şablon.
- Hiçbir kapı (gate) atlanmaz: soft gate bile görünür kalır.

## §3 YAML — CI Pipeline (disk: ci.yml)

### §3.1 Güvenlik ve tekillik ayarları (`ci.yml:14-21`)

```yaml
# KANIT: ci.yml:14 (permissions) · :17 (concurrency) · :21 (env)
# Kural D-17/D-18: en az yetki + eşzamanlılık iptali (master §B.1/17-18).
name: CI Pipeline

on:            # tetikleyiciler (push/PR)
  # ...

permissions:
  contents: read   # en az yetki — write yok (D-17)

concurrency:
  group: ci-${{ github.ref }}   # aynı ref'te tekillik (D-18)
  cancel-in-progress: true      # eskisi iptal edilir

env:            # ortak ortam değişkenleri
  # ...
jobs:
  # ...
```

### §3.2 Statik analiz + test kapıları (`ci.yml:62/68/104`)

```yaml
# KANIT: ci.yml:62 (PHPStan) · :68 (needs) · :104 (PHPUnit)
# Kural: lint önce, test sonra (D-11 bağımlılık); seviye 5 (D-8).
jobs:
  php-lint:
    name: PHP Lint
    runs-on: ubuntu-latest
    steps:
      - name: PHPStan (composer stan)   # :62 — level=5, script esas (D-8/D-9)
        run: composer stan

  php-test:
    name: PHP Tests (${{ matrix.project }})   # :67 — matrix (D-26 hedefleri)
    needs: php-lint                            # :68 — bağımlılık (D-11)
    runs-on: ubuntu-latest
    steps:
      - name: PHPUnit                          # :104 — test kapısı
        run: composer test
```

### §3.3 Soft gate'ler (`ci.yml:161-183`)

```yaml
# KANIT: ci.yml:161 (JS Coverage — soft, A7) · :183 (JS E2E — soft, A6)
# Kural D-12/D-13: continue-on-error — başarısızlık pipeline'ı kırmaz, GÖRÜNÜR kalır.
jobs:
  js-coverage:
    name: JS Coverage (gate — soft, A7)
    runs-on: ubuntu-latest
    continue-on-error: true        # :167 — yumuşak kapı (D-12)
    steps:
      - name: Vitest coverage      # :175
        run: npm run test:coverage

  js-e2e:
    name: JS E2E (Playwright — soft, A6)
    runs-on: ubuntu-latest
    continue-on-error: true        # :183 — yumuşak kapı (D-13)
    steps:
      - name: Playwright e2e       # :202
        run: npm run test:e2e
```

## §4 YAML — Secret Scan (disk: secret-scan.yml)

```yaml
# KANIT: secret-scan.yml:11-12 (permissions) · :14-16 (concurrency) · :21 (runner) · :26 (fetch-depth)
# Kural D-17/D-18/D-19: en az yetki · iptal · TAM GEÇMİŞ taraması.
name: Secret Scan

permissions:
  contents: read                 # :11-12 — en az yetki (D-17)

concurrency:
  group: secret-scan-${{ github.ref }}
  cancel-in-progress: true       # :14-16 (D-18)

jobs:
  gitleaks:
    runs-on: ubuntu-latest      # :21
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0        # :26 — tam geçmiş taranır (D-19)
      - uses: gitleaks/gitleaks-action@v2
        # config: kök .gitleaks.toml otomatik kullanılır (D-20, config:2)
```

## §5 TOML — Gitleaks Config (disk: .gitleaks.toml, 18 satır)

```toml
# KANIT: .gitleaks.toml:1-18
# Kural D-21 (ADR-034): allowlist YALNIZ test dosyaları — üretim asla girmez.
title = "CoreMusic gitleaks"

[extend]
useDefault = true                # :8-9 — varsayılan kural seti

[allowlist]
description = "Test dosyasindaki sahte/ornek sifreler (ADR-034 §5.2/5) - uretim kodu asla allowlist girmez"
paths = [
  '''auth\.coremusic\.net/tests/Unit/Domain/DTO/LoginRequestTest\.php$''',   # :14
  # JWT test fixture keypair (ADR-095): yalniz shared/tests/Fixtures/jwt/
  '''shared/tests/Fixtures/jwt/test2?-private\.pem$''',                      # :17
]
# Üretim/ortam allowlist'i YASAK; bulgu = PR engeli (config:3-4, ADR-034 §4.1-R6).
```

## §6 PowerShell — Deployment kapısı (disk: deployment.md bağlamı)

```powershell
# KANIT: .workflows/deployment.md:126-199 (8 aşama) · :212 (rollback) · :260 (insan onayı)
# Kural D-22/D-23: onaylı yayına alma + geri alma; otomatik CD ADR bekliyor (§8/1).
param(
    [Parameter(Mandatory = $true)][ValidateSet('staging','production')][string]$Stage,
    [switch]$Rollback
)

$ErrorActionPreference = 'Stop'

if ($Rollback) {
    # Aşama değil: §4 Geri Dönüş — son iyi sürüme dön (deployment.md:212).
    Invoke-GitCheckoutTag -Tag (Get-LastGoodTag -Stage $Stage)
    exit 0
}

# 8 aşamalı akış (deployment.md:126-199) — insan onayı AŞAMA içinde zorunlu (D-22).
$stages = Get-DeploymentStages -Stage $Stage
foreach ($s in $stages) {
    if ($s.RequiresHumanApproval) {
        $ok = Read-Host "Onay: $($s.Name) — devam? (evet/hayir)"   # :260 yasağı
        if ($ok -ne 'evet') { exit 1 }   # onaysız üretime dağıtım YASAK
    }
    Invoke-Stage -Stage $s
}
```

## §7 PHP 8.4 — Sürüm/ortağın doğrulanması (iskele)

```php
<?php
declare(strict_types=1);

/**
 * K17 iskeleti (şablon): composer.json sürüm gerçeği CI'a türetilir.
 * Kural D-7: PHP sürümü tahmin edilmez, composer.json'dan okunur.
 */
final class ReleaseGate
{
    public static function phpVersionFromComposer(string $composerJsonPath): string
    {
        $raw = file_get_contents($composerJsonPath);
        if ($raw === false) {
            // Fail-closed: okunamayan konfig CI'ı kırmalı, sessiz geçmez.
            throw new \RuntimeException('composer.json okunamadı');
        }
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        // "php": "^8.4" gibi bir kısıt → D-7: türetme, tahmin yok.
        return (string)($data['require']['php'] ?? '');
    }
}
```

## §8 JavaScript (Node) — Sürüm notu doğrulayıcı (iskele)

```javascript
// K17 iskeleti (şablon): Keep a Changelog biçimi kapısı.
// Kural: changelog kategorileri sabittir; bilinmeyen kategori = hata.
'use strict';

const ALLOWED = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];

function validateChangelog(md) {
  const lines = md.split('\n');
  let bad = [];
  for (const line of lines) {
    const m = line.match(/^###\s+(.+)$/);
    if (m && !ALLOWED.includes(m[1].trim())) {
      // Bilinmeyen kategori geçiştirilmez (fail-closed, kapalı liste).
      bad.push(m[1]);
    }
  }
  return { ok: bad.length === 0, bad };
}

module.exports = { validateChangelog, ALLOWED };
```

## §9 TypeScript — SemVer artırımı (iskele)

```typescript
// K17 iskeleti (şablon): SemVer 2.0.0 tip güvenli artırım.
// Kural: sürüm alanı dışı değer compile/runtime hatasıdır.
export type Bump = 'major' | 'minor' | 'patch';

export interface SemVer { major: number; minor: number; patch: number; }

export function bump(v: SemVer, kind: Bump): SemVer {
  switch (kind) {
    case 'major': return { major: v.major + 1, minor: 0, patch: 0 };
    case 'minor': return { major: v.major, minor: v.minor + 1, patch: 0 };
    case 'patch': return { major: v.major, minor: v.minor, patch: v.patch + 1 };
  }
}

export function format(v: SemVer): string {
  return `${v.major}.${v.minor}.${v.patch}`;   // x.y.z (SemVer 2.0.0)
}
```

## §10 Python — Ortam kapı doğrulayıcı (iskele)

```python
# K17 iskeleti (şablon): gerekli ortam değişkenleri DOĞRULANIR.
# Kural: eksik secret = dağıtım durur (fail-closed); boş değer kabul edilmez.
REQUIRED_BY_STAGE = {
    "production": ["DB_DSN", "APP_SECRET", "JWT_KEY"],
    "staging": ["DB_DSN", "APP_SECRET"],
}


def gate(stage: str, env: dict) -> list[str]:
    """Eksik anahtarları döndürür; boş liste = kapı geçildi."""
    required = REQUIRED_BY_STAGE.get(stage)
    if required is None:
        # Bilinmeyen ortam: fail-closed (3↔4 ortam kararı ADR bekliyor, §8/4).
        return [f"<bilinmeyen ortam: {stage}>"]
    return [k for k in required if not env.get(k)]


def can_deploy(stage: str, env: dict) -> bool:
    missing = gate(stage, env)
    if missing:
        print("DEPLOY REDDEDİLDİ — eksik:", ", ".join(missing))
        return False
    return True
```

## §11 C — Derleme zamanı sürüm damgası (iskele)

```c
/* K17 iskeleti (şablon): artefakta sürüm damgası (izlenebilirlik).
 * Kural: hangi sürüm çıktığını binary söyler — tahmin yok. */
#include <stdio.h>

#ifndef CM_VERSION
#define CM_VERSION "0.0.0-unknown"   /* bilinmeyen = görünür, gizlenmez */
#endif

void cm_print_build_info(void) {
    /* Sürüm + derleme zamanı — deploy rollback'te etiket olarak kullanılır
     * (deployment.md:212 rollback bağlamı). */
    printf("coremusic version=%s built=%s\n", CM_VERSION, __DATE__ " " __TIME__);
}

/* Kural: taşma yok — snprintf sınırı zorunlu. */
int cm_format_artifact(char *buf, size_t n) {
    int w = snprintf(buf, n, "coremusic-%s", CM_VERSION);
    if (w < 0 || (size_t)w >= n) {
        return -1;   /* fail-closed */
    }
    return w;
}
```

## §12 C++ — CI artefaktı adı (iskele)

```cpp
// K17 iskeleti (şablon): artefakt adı üretimi (D-CD-2 öncesi iskele).
// Kural: ad tahmin edilmez — commit + sürüm birlikte taşınır.
#include <string>

namespace ci {

struct BuildInfo {
    std::string version;   // SemVer
    std::string commit;    // tam hash
};

inline std::string artifactName(const BuildInfo& b) {
    // Örnek: coremusic-1.4.2+c46f08ac — rollback'te etiket geri alınabilir.
    if (b.version.empty() || b.commit.empty()) {
        // Eksik bilgi: geçersiz ad üretme, çağırıcı hataya düşsün (fail-closed).
        throw std::invalid_argument("version/commit required");
    }
    return "coremusic-" + b.version + "+" + b.commit.substr(0, 8);
}

}  // namespace ci
```

## §13 C# — Deployment kontrol listesi (iskele)

```csharp
// K17 iskeleti (şablon): 8 aşamalı checklist (deployment.md:126-199 bağlamı).
using System.Collections.Generic;

namespace CoreMusic.Deployment;

public sealed class Checklist
{
    // deployment.md 8 aşama — gerçek metin dosyadan okunur, kopyalanmaz.
    private static readonly string[] Stages =
    {
        "on-kontrol", "surum-dogrula", "test-kapisi", "secret-kontrol",
        "yayinla", "saglik-kontrol", "log-izle", "kapanis",
    };

    public IReadOnlyList<string> Remaining(IReadOnlySet<string> done)
    {
        var left = new List<string>();
        foreach (var s in Stages)
            if (!done.Contains(s))
                left.Add(s);   // eksik aşama görünür kalır (yutma yok)
        return left;
    }

    public bool CanClose(IReadOnlySet<string> done) => Remaining(done).Count == 0;
}
```

## §14 Go — Yayından sonra sağlık bekleme (iskele)

```go
// K17 iskeleti (şablon): deploy sonrası health poll.
// Kural: sağlık gelmezse yayın BAŞARISIZ sayılır (rollback tetiklenir).
package deploy

import (
	"context"
	"fmt"
	"net/http"
	"time"
)

// WaitHealthy: url yeşil olana kadar bekle; zaman aşımında hata (fail-closed).
func WaitHealthy(ctx context.Context, url string, timeout time.Duration) error {
	ctx, cancel := context.WithTimeout(ctx, timeout)
	defer cancel()

	for {
		req, _ := http.NewRequestWithContext(ctx, http.MethodGet, url, nil)
		resp, err := http.DefaultClient.Do(req)
		if err == nil {
			_ = resp.Body.Close()
			if resp.StatusCode == http.StatusOK {
				return nil // yayın ayakta
			}
		}
		select {
		case <-ctx.Done():
			return fmt.Errorf("saglik kontrolu basarisiz: %s: %w", url, ctx.Err())
		case <-time.After(2 * time.Second):
		}
	}
}
```

## §15 Rust — Secret kapısı (iskele)

```rust
// K17 iskeleti (şablon): CI'da sır sızıntısı kontrolü (gitleaks tamamlayıcısı).
// Kural D-21: üretim değeri hiçbir zaman allowlist'e girmez.
pub const MASK: &str = "[REDACTED]";

#[derive(Debug, PartialEq)]
pub enum Finding {
    TestOnly,        // test dosyası → izin (allowlist, D-21)
    Production,      // üretim → ENGEL (bulgu = PR engeli)
}

pub fn classify(path: &str, is_test_fixture: bool) -> Finding {
    // Allowlist yalnız test yolları (config:13-18 ruhu).
    if is_test_fixture && path.contains("/tests/") {
        Finding::TestOnly
    } else {
        Finding::Production   // fail-closed
    }
}

pub fn gate(findings: &[Finding]) -> Result<(), usize> {
    let blocked = findings.iter().filter(|f| **f == Finding::Production).count();
    if blocked > 0 {
        return Err(blocked); // PR durur
    }
    Ok(())
}
```

## §16 Java — Gradle/Maven sürüm doğrulama iskeleti

```java
// K17 iskeleti (şablon): build sürümü doğrulama (artefakt izlenebilirliği).
// Kural: "unknown" sürüm yayına girmez (D-CD-2 öncesi iskele).
package com.coremusic.ci;

public final class ReleaseGate {

    public static boolean isReleaseable(String version) {
        if (version == null || version.isBlank()) {
            return false;                 // fail-closed: boş sürüm
        }
        // SemVer minimal doğrulama: x.y.z [+meta]
        return version.matches("\\d+\\.\\d+\\.\\d+([-.][0-9A-Za-z.-]+)?");
    }

    public static String requireReleaseable(String version) {
        if (!isReleaseable(version)) {
            // Bilinmeyen/yayına elverişsiz sürüm görünür hatadır, gizlenmez.
            throw new IllegalStateException("yayina elverissiz surum: " + version);
        }
        return version;
    }
}
```

## §17 SQL — Migration kapısı (iskele, K007 kesişimi)

```sql
-- K17 iskeleti (şablon): migration kapısı sorgusu.
-- Kural: parametreli sorgu zorunlu; migration envanteri okunur, tahmin edilmez.
SELECT
    m.id,
    m.name,
    m.applied_at,
    m.checksum
FROM schema_migration AS m
WHERE m.name = ?                 -- parametreli
ORDER BY m.applied_at DESC
LIMIT 1;

-- Deploy kapısı: bekleyen migration varsa yayın durur (expand-and-contract).
SELECT COUNT(*) AS pending
FROM schema_migration AS m
WHERE m.applied_at IS NULL;      -- pending > 0 → release gate kapanır
```

## §18 Shell (Bash) — Release scripti (iskele, 13. dil)

```bash
#!/usr/bin/env bash
# K17 iskeleti (şablon): release — sürüm notu + etiket + kapı.
# Kural: her adımın çıkış kodu kontrol edilir; hata yutulmaz (fail-closed).
set -euo pipefail

VERSION="${1:?kullanım: release.sh <semver>}"

# 1) SemVer biçimi (tahmin yok, doğrula).
[[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || { echo "gecersiz surum: $VERSION"; exit 1; }

# 2) Changelog kategorileri geçerli mi (Keep a Changelog).
node -e "require('./scripts/validate-changelog').validateChangelog(require('fs').readFileSync('CHANGELOG.md','utf8'))"

# 3) Kapılar: testler geçmediyse etiket ATILMAZ (K18 bağımlılığı).
npm run test:unit

# 4) Etiket (rollback referansı — deployment.md:212).
git tag -a "v$VERSION" -m "release v$VERSION"
echo "v$VERSION etiketlendi — publish onayı deployment.md 8 asamadan gecer."
```

## §19 Diller ve Kural Eşlemesi

| # | Dil | Blok | Kural |
|---|-----|------|-------|
| 1 | YAML | §3 (3) · §4 | disk: yetki/iptal/soft-gate/needs (D-11…D-19) |
| 2 | TOML | §5 | disk: allowlist (D-21 · ADR-034/095) |
| 3 | PowerShell | §6 | disk bağlam: onay + rollback (D-22/D-23) |
| 4 | PHP 8.4 | §7 | D-7 sürüm türetme |
| 5 | JavaScript | §8 | changelog kapısı |
| 6 | TypeScript | §9 | SemVer 2.0.0 |
| 7 | Python | §10 | secret/ortam kapısı |
| 8 | C | §11 | artefakt damgası |
| 9 | C++ | §12 | artefakt adı |
| 10 | C# | §13 | 8 aşama checklist |
| 11 | Go | §14 | health poll (fail-closed) |
| 12 | Rust | §15 | sır kapısı (D-21) |
| 13 | Java | §16 | sürüm gate |
| 14 | SQL | §17 | migration gate |
| 15 | Bash | §18 | release scripti |

## §20 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

- ⚠️ §7–§18 **iskele/şablondur** — diskte production karşılığı **UNKNOWN**
  (CD modeli ADR bekliyor, §8/1); taşınma kararı ADR'den geçer.
- ⚠️ §6'daki aşama adları **temsilîdir** — gerçek metin `deployment.md:126-199`'da
  okunmalı (bu blok metin kopyalamaz).
- ⚠️ §14/§17 kapıları ancak CD modeli kapatıldığında üretime girer.
- ⚠️ §3 YAML pencereleri 2026-10-18 grep'iyle doğrulandı.

## §21 İlgili Dosyalar

- `devops-bilesenler.md` — C-01…C-04 eşlemesi
- `devops-kararlar.md` — D-1…D-30 kararları
- `devops-test-stratejisi.md` — kapıların sınaması
- `.github/workflows/ci.yml` · `secret-scan.yml` · `.gitleaks.toml`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
