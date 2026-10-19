---
node_id: K17-devops/cd
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/source-control
  - K17-devops/environments
  - K17-devops/rollback
  - K17-devops/pipeline
evidence:
  - ".workflows/deployment.md · tam dosya okundu — 281 satır (2026-10-17)"
  - ".github/workflows/ci.yml · tam dosya okundu — 203 satır · deploy job YOK (2026-10-17)"
  - "tüm .github/workflows/*.md/.yml içinde 'environment:' / 'deploy' / 'tags' = 0 isabet (2026-10-17)"
  - "workflow envanteri: ci.yml (203) + secret-scan.yml (31) — toplam 2 dosya (2026-10-17)"
validation:
  - "Bugünkü durum tamamı disk ölçümünden: deploy job yok, tetikleyici yalnız push/pull_request"
  - "Hedef durum kullanıcının seçtiği 'tam otomatik CD (trunk-based)' — çelişki append-only kaydedildi"
  - "Tüm workflow YAML'ları açıkça TASLAK olarak işaretlendi (beyaz-liste dışı)"
open_questions:
  - "Tam otomatik CD, deployment.md:260 'İnsan onayı olmadan üretime dağıtma' yasağıyla çelişir — ADR bekliyor"
  - "CD hangi artefakt modeliyle? (git-snapshot, artifact-registry, imaj) — diskte kanıt yok, ADR bekliyor"
group: devops
---

# CD — Continuous Delivery / Deployment (K17-devops altı)

> **Bu dosya ne değildir:** `.yml` uygulaması değildir. Altındaki YAML blokları **TASLAK**tır
> ve beyaz-liste dışıdır (`*.yml` yazımı onay + ADR ister — `devops-master.md` §C.1/25-27).
> **Sorumluluk:** DevOps Engineer · **Otorite:** Bayram Ali / Vault Steward ·
> **Mod:** Red Team · Human Mode · Truth Mode

---

## §1 Özet — Bugün ne var, hedef ne

| Boyut | Bugün (disk kanıtı) | Hedef (kullanıcı kararı 2026-10-17) |
|---|---|---|
| CD job'ı | **YOK** — deploy job'ı hiç yok | Tam otomatik CD (trunk-based) |
| Tetikleyici | yalnız `push:` + `pull_request:` (ci.yml:10-12) | `push: branches:[main]` → build → staging → prod |
| Deployment environment | **YOK** — `environment:` 0 isabet | `staging` + `production` environment'ları |
| Release tetikleyici | **YOK** — `tags:`/`release` 0 isabet | tag tabanlı release (bkz. `release-management/`) |
| İnsan onayı | Süreçte manuel 8 aşama (deployment.md) | Prod'da GitHub `required reviewer` koruması |
| Geri alma | Süreçte §4 yazıyor; otomatik tetikleyici **YOK** | Health-check başarısızsa otomatik rollback |

**Toplam:** bugün CI **vardır** (2 workflow, 105 satır), CD **yoktur**. Aradaki mesafe = tek
deploy job'ı + environment'lar + artefakt zinciri + rollback tetikleyicisi.

---

## §2 Kapsam ve Kapsam Dışı

| Kapsam | Kapsam Dışı |
|---|---|
| Deploy job tasarımı, artefakt akışı, environment'lar | Test kapıları → `pipeline/` + `ci/` |
| Release → deploy ilişkisi | Release stratejisi/karar matrisi → `release-management/` |
| Rollback **tetikleyicisi** (CD içi) | Rollback süreç/taktik detayı → `rollback/` |
| Ortam değişkenleri → deploy'un girdisi | Ortam envanteri/koruma kuralları → `environments/` |
| Branch → artefakt kaynak modeli | Git hijyeni/branching → `source-control/` |

**Çapraz:** `deployment/` dizini `yayilim-stratejileri.md` (502s) + `rollback-ve-geri-alma.md`
(534s) + `cicd-asamalari.md` (511s) + `konteyner-imaj.md` (511s) + `secret-yonetimi.md` (509s)
+ `pipeline-hizlandirma.md` (513s) içerir. Bu `cd/index.md` **içeriği tekrarlamaz** — yalnız
indirger ve boşlukları işaretler.

---

## §3 Bugünkü CD Durumu (disk kanıtı — 2026-10-17)

### §3.1 Deploy job'ı yok

```powershell
# Kanıt (2026-10-17 grep çıktısı — birebir)
# Şablon: .github/workflows/*.yml içinde 'environment:' / 'deploy' / 'tags:'/'release' tetikleyici
# Sonuç: 0 isabet
# ci.yml tetikleyicisi (satır 10-11):
on:
  push:
  pull_request:
```

Yorum: tetikleyici filtresizdir ama **içerik** yalnız doğrulamadır. `ci.yml` 6 job'ının
hepsi lint/test/audit — hiçbiri dağıtımı çalıştırmaz. `secret-scan.yml` de yalnız taramadır.
**Yani: pipeline 'CI' ile durur; 'CD' başlamamıştır.**

### §3.2 İnsan-onaylı manuel süreç (deployment.md)

Gerçek süreç `deployment.md` §3 akış diyagramında **8 aşamadır** (Gate değil — bkz.
`devops-ornekler.md` §6):

```
1 ÖN KONTROLLER → 2 GÜVENLİK → 3 TEST → 4 ONAY → 5 DAĞITIM → 6 SAĞLIK → 7 İZLEME → 8 LOG
```

`deployment.md:156-163` (Aşama 4 — Onay) risk-derinliği tablosu:

| Risk Seviyesi | Onay |
|---|---|
| Düşük | Agent self-review |
| Orta | Agent + peer review |
| Yüksek | İnsan onayı |
| Kritik | İnsan + güvenlik + mimari onay |

`deployment.md:176-180` (Aşama 5 — Dağıtım adımları): `1 Yedekleme · 2 Migration · 3 Dosya
yükleme · 4 Config güncelleme · 5 Servis başlatma`.

**Bugün: CD = insanın eliyle.** Automation yalnızca doğrulama katmanındadır (CI).

---

## §4 Hedef Model — Tam Otomatik CD (trunk-based)

> **Kaynak:** Kullanıcı kararı, 2026-10-17. **Çelişki:** `deployment.md:260` "İnsan onayı
> olmadan üretime dağıtma" — §6'da append-only kayıtlıdır.

### §4.1 Trunk-based + tam otomatik CD prensipleri

1. **Tek dal (`main`) + kısa ömürlü feature branch'ler** (≤2 gün) → `source-control/`.
2. **Her `main` push'u** doğrudan aday sürümdür.
3. **Aşamalı yayılım (progressive):** build → staging → prod, arada health-check.
4. **Fail-closed:** health-check başarısızsa otomatik rollback; prod için GitHub environment
   `required reviewer` son kapıdır (tam otomasyon + insan kapısı birlikte).
5. **Her deploy geri alınabilir olmak zorundadır** — rollback CD'nin parçası, sonradan eklenen
   özellik değil (`rollback/`).

### §4.2 Zaman çizelgesi (bugün → hedef)

```
TODAY  : push → CI (6 job) → [ELLE deployment.md 8 aşama] → prod
TARGET : push → CI (6 job) → build artefakt → staging deploy → health/smoke → prod
         (environment: production + required reviewer) → izleme → [hata: otomatik rollback]
```

---

## §5 Artefakt Akışı (branch → artefakt → ortam)

> **Boşluk:** CoreMusic'te artefakt modeli (git-snapshot mı, artifact-registry mi, konteyner
> imaj mı) diskte **kanıtlanamıyor**. `konteyner-imaj.md` (511s) var ama Dockerfile/imaj
> kancası `.ai/architecture/K17-devops/` dışında. **→ ADR bekliyor.**

| Model | Ne taşır | Avantaj | Çekince |
|---|---|---|---|
| A. git-snapshot | commit SHA → deploy | en basit, tek kanıt noktası | her deploy `git pull`; migration riski |
| B. artifact (npm/composer dist) | `dist/` + `manifest.json` | CI'da üretilir, aynısı üretime gider | artefakt saklama/imza gerektirir |
| C. konteyner imaj | digest-pinli imaj | immutability, kolay rollback | altyapı maliyeti, diskte kanıt yok |

**Çekici kanıt (partial):** `devops-ornekler.md` §10.2 taslağında `dist/manifest.json` +
`rsync` kullanılmıştır — bu **B modeline** yatkın bir varsayımdır, doğrulanmış karar değil.

---

## §6 Taslak — Deploy Workflow (UYGULANMADI — onay + ADR bekliyor)

```yaml
# === TASLAK — uygulanmadı · onay + ADR bekliyor ===
# Hedef mimari: §4 (tam otomatik CD, trunk-based)
# Beyaz-liste: *.yml yazımı orkestratör yetkisinde (devops-master.md §C.1/25-27)
name: CD Deploy

on:
  push:
    branches: [ main ]        # trunk-based: main her zaman aday sürümdür

permissions:
  contents: read               # deploy için yazma yetkisi GEREKMEZ (en az yetki)

concurrency:
  group: cd-production
  cancel-in-progress: false    # CD'de iptal YOK: yarım kalmış dağıtım tehlikelidir

jobs:
  build:
    runs-on: ubuntu-latest
    timeout-minutes: 15
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '26'
          cache: npm
      - run: npm ci
      - run: npm run test:js
      - uses: actions/upload-artifact@v4
        with:
          name: dist
          path: dist/
          retention-days: 30

  deploy-staging:
    needs: build
    runs-on: ubuntu-latest
    environment: staging       # environments/index.md §4
    steps:
      - uses: actions/download-artifact@v4
        with: { name: dist }
      - run: ./scripts/deploy.sh staging
      - run: ./scripts/smoke.sh staging

  deploy-production:
    needs: deploy-staging
    runs-on: ubuntu-latest
    environment: production     # GitHub koruması: required reviewer (§4.1/4)
    steps:
      - uses: actions/download-artifact@v4
        with: { name: dist }
      - run: ./scripts/deploy.sh production
      - run: ./scripts/smoke.sh production
```

**Neden `environment: production` gerekli:** GitHub deployment environment'ları, `required
reviewer` koruma kuralı tanımlayarak **otomasyonun içinde insan kapısı** kurar. Bu, hem
`deployment.md:260` yasağıyla kısmen uzlaşır (son dokunuş insan), hem tam otomasyonun
hızını korur (önceki aşamalar otomatik).

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Çelişki | Kaynak A | Kaynak B | Durum |
|---|---|---|---|---|
| 1 | **Tam otomatik CD vs insan onayı yasağı** | Kullanıcı kararı 2026-10-17 (§4) | `deployment.md:260` "İnsan onayı olmadan üretime dağıtma" | **AÇIK — ADR + deployment.md revizyonu bekliyor.** Metin silinmedi |
| 2 | **CD job'ı yok vs hedef CD** | ci.yml (deploy job YOK, §3.1) | §4 hedef mimari | **Tasarım boşluğu** — §6 taslağı bu yüzden TASLAK |
| 3 | **Artefakt modeli belirsiz** | §5 üç model | diskte kanıt yok | **ADR bekliyor** |
| 4 | **3 ortam vs 4 ortam** | `deployment.md:167-171` (3 ortam) | kullanıcı kararı: 4 ortam (local/dev/staging/prod) | → `environments/index.md` §7 |
| 5 | **konteyner imaj disk kanıtı** | `konteyner-imaj.md` (511s, var) | Dockerfile/imaj kancası kanıtlanamadı | → `build/index.md` §7 |

---

## §8 Çok-Dilli Uygulama Örnekleri (TASLAK)

> Hepsi **uygulanmamış**tır; yalnız `cd/` hedef mimarisini somutlaştırmak içindir.

### §8.1 Bash — deploy + health-check + rollback zinciri

```bash
#!/usr/bin/env bash
# TASLAK — cd/index.md §4 (deployment.md Aşama 5/6)
set -euo pipefail
ENV="${1:?kullanım: ./deploy.sh <staging|production>}"

# 1) Artefakt kanıtı (deployment.md:176 "Yedekleme al" öncesi zorunlu)
test -f dist/manifest.json || { echo "manifest yok"; exit 1; }

# 2) Pre-migration yedek (Aşama 5, adım 1)
backup="backup/pre-$(date +%F-%H%M%S).sql"
mysqldump --single-transaction --routines --triggers coremusic > "$backup"

# 3) Dağıtım (Aşama 5, adım 3-5)
rsync -az --delete dist/ "/var/www/$ENV/"
systemctl reload "php-fpm@$ENV" 2>/dev/null || true

# 4) Health-check (Aşama 6: HTTP 200 / API / DB / log / performans)
for i in $(seq 1 5); do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://$ENV.coremusic.net/healthz" || true)
  [ "$code" = "200" ] && { echo "OK ($code)"; exit 0; }
  echo "deneme $i/5 kod=$code"; sleep 5
done

echo "health başarısız → rollback"; ./rollback.sh "$ENV" "$backup"; exit 1
```

### §8.2 PowerShell — Windows deploy doğrulama

```powershell
# TASLAK — cd/index.md §4; CI'dan önce yerel fail-fast
$ErrorActionPreference = 'Stop'
$env_ = if ($args) { $args[0] } else { 'staging' }

if (-not (Test-Path 'dist/manifest.json')) { throw 'manifest yok' }
$m = Get-Content 'dist/manifest.json' -Raw | ConvertFrom-Json
Write-Host "[deploy] artefakt v$($m.version) · commit $($m.commit)"

# health-check
$r = try { Invoke-WebRequest "https://$env_.coremusic.net/healthz" -TimeoutSec 20 -UseBasicParsing } catch { $null }
if (-not $r -or $r.StatusCode -ne 200) { throw "health FAIL ($($r.StatusCode))" }
Write-Host '[deploy] health OK' -ForegroundColor Green
```

### §8.3 PHP — health endpoint (deployment.md Aşama 6)

```php
<?php
declare(strict_types=1);
// TASLAK — cd/index.md §4 · Aşama 6 "Veritabanı | Bağlantı sağlıklı mı?"
require __DIR__ . '/vendor/autoload.php';

use CoreMusic\Database\DatabaseManager;

header('Content-Type: application/json');
$checks = [];
$checks['process'] = ['ok' => true];

try { DatabaseManager::instance()->pdo()->query('SELECT 1'); $checks['database'] = ['ok' => true]; }
catch (Throwable $e) { $checks['database'] = ['ok' => false, 'error' => $e->getMessage()]; }

$healthy = array_reduce($checks, fn (bool $c, array $x) => $c && ($x['ok'] ?? false), true);
http_response_code($healthy ? 200 : 503);
echo json_encode(['healthy' => $healthy, 'checks' => $checks], JSON_UNESCAPED_SLASHES);
```

### §8.4 JavaScript — deploy sonrası smoke (Aşama 6 "HTTP 200 | Ana sayfa")

```javascript
// TASLAK — cd/index.md §4; fail-closed → rollback zinciri
import { chromium } from 'playwright';

const BASE = process.env.DEPLOY_URL ?? 'https://staging.coremusic.net';
const PATHS = ['/', '/auth/login', '/music'];
const browser = await chromium.launch();
const page = await browser.newPage();
const failures = [];

for (const path of PATHS) {
  const res = await page.goto(BASE + path, { waitUntil: 'domcontentloaded', timeout: 20000 });
  const status = res?.status() ?? 0;
  const ok = status >= 200 && status < 400;
  console.log(`[smoke] ${status} ${path}`);
  if (!ok) failures.push({ path, status });
}
await browser.close();
if (failures.length) { console.error(failures); process.exit(1); }
console.log(`[smoke] ${PATHS.length}/${PATHS.length} OK`);
```

### §8.5 SQL — geri alma sorgusu (rollback tetikleyici CD içi)

```sql
-- TASLAK — cd/index.md §4 rollback zinciri · rollback/index.md §5
-- Deployment sonrası veri bozulması: pre-deploy yedeğinden hedef tablolar
-- (geri alma yalnız migration/veri içindir; kod rollback'i git/artifact tarafından yapılır)
START TRANSACTION;
-- ... yedekten hedef tablo geri yükleme ...
COMMIT;
-- DOĞRULAMA: kayıt sayıları pre-deploy yedeği ile eşleşmeli
SELECT COUNT(*) FROM users;   -- eşleşme yoksa rollback-in-rollback: manuel eskalasyon
```

---

## §9 İlişkiler

| Hedef | Ne için |
|---|---|
| `ci/index.md` | Deploy öncesi doğrulama kapıları |
| `pipeline/index.md` | CI+CD tek şeritte akış |
| `environments/index.md` | `environment:` koruma kuralları (§6) |
| `release-management/index.md` | tag → aday sürüm (§4.2) |
| `rollback/index.md` | CD rollback zincirinin süreç tarafı (§7/5) |
| `source-control/index.md` | trunk-based dal modeli (§4.1/1) |
| `build/index.md` | artefakt üretimi (§5) |
| `deployment/` içerik | yayilim-stratejileri · rollback-ve-geri-alma · cicd-asamalari · konteyner-imaj · secret-yonetimi · pipeline-hizlandirma |
| `.workflows/deployment.md` | bugünkü manuel 8 aşama (§3.2) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17 · ilk tam yazım) · **Kapsam:** yalnız `.ai/architecture/K17-devops/**` ·
**Yasaklar:** `git commit` YOK · `*.yml` yazımı YOK (§6/§8 yalnız TASLAK) · `deployment.md` düzenlenmedi
