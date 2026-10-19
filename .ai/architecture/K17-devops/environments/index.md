---
node_id: K17-devops/environments
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/cd
  - K17-devops/secret-yonetimi
evidence:
  - ".workflows/deployment.md satır 167-171 · tam dosya okundu — 3 ortam tablosu (2026-10-17)"
  - "config/domain.php · 7 subdomain (auth, home, assets, music, admin, media, api) — shared/AGENTS.md §2 envanteri"
  - "GitHub deployment environment koruma kuralları: docs.github.com/en/actions/deployment/targeting-different-environments/using-environments-for-deployment (2026-10-17 erişildi)"
validation:
  - "Bugünkü 3 ortam deployment.md'den; 4. ortam (local) KULLANICI KARARI — genişleme kaydı §7"
  - "Environment koruma taslağı GitHub resmi dokümanıyla çapraz doğrulandı"
  - "subdomain ↔ environment eşleşmesi disk kanıtı (config/domain.php 7 domain)"
open_questions:
  - "staging ortamı için ayrı subdomain/alt-alan adı var mı? — config/domain.php'de 'staging'/'test' domaini YOK, ADR bekliyor"
  - "4 ortam deployment.md §5 ile çelişir — ADR kaydı bekliyor"
group: devops
---

# Environments — Ortam Envanteri ve Koruma Kuralları (K17-devops altı)

> **Karar (kullanıcı, 2026-10-17): 4 ortam** — `local · development · staging · production`.
> Bu, `.workflows/deployment.md` §5'teki **3 ortamı (Geliştirme/Test/Üretim) genişletir** →
> §7'de append-only çelişki/ADR kaydı. **Sorumluluk:** DevOps Engineer · **Otorite:** Bayram Ali

---

## §1 Özet Tablo — 4 Orta

| # | Ortam | Amaç | Tetikleyici | Veri hassasiyeti | İnsan onayı | Disk kanıtı |
|---|---|---|---|---|---|---|
| 1 | **local** | Geliştirme makinesi | `composer install` / `npm ci` yerel | Üretim verisi **YASAK** | — | `.env` (gitignore:6) |
| 2 | **development** | Ortak dev sunucusu / dal incelemesi | `push:` (her dal) | sentetik | hayır | deployment.md:169 |
| 3 | **staging** | Prod'a benzeyen doğrulama | `main` → CI geçince | anonimleştirilmiş | hayır (auto) | ADR bekliyor |
| 4 | **production** | Canlı kullanıcı | staging green + sağlk | gerçek | **EVET** (`required reviewer`) | deployment.md:171 |

**Kural:** gerçek kullanıcı verisi yalnız **production**'dadır. development/staging'e
üretim verisi kopyalanmaz (KVKK/GDPR + `.gitleaks.toml` ruhu — §5).

---

## §2 Bugünkü durum (disk kanıtı)

### §2.1 deployment.md'nin 3 ortamı (§3 — gerçekte Aşama değil, doğrudan ortam listesi)

```markdown
# Dosya: .workflows/deployment.md · satır 167-171 (birebir alıntı)
**Dağıtım ortamları:**

| Ortam | Hedef |
|-------|-------|
| Geliştirme | Local development |
| Test | Test sunucusu |
| Üretim | production.coremusic.net |
```

**İki isim uyuşmazlığı** (çelişki — §7/2):
- deployment.md "Geliştirme → Local development" diyor = **local ve development'ı tek satırda**
  birleştiriyor; bu yüzden 3 sayıyor. 4 ortam ayrımı aslında metnin *içinde* saklı.
- deployment.md "Test" diyor, kullanıcı "staging" — aynı rol, farklı ad.

### §2.2 Deployment environment'ları workflow'da YOK

`environment:` anahtarı `.github/workflows/*.yml` içinde **0 isabet** (2026-10-17 grep).
Yani GitHub'ın environment koruma özellikleri **hiçbiri aktif değil**.

### §2.3 Ortak subdomain'ler (config/domain.php — 7 domain)

`auth · home · assets · music · admin · media · api` (shared/AGENTS.md §2 envanteri kaynaklı).

**Boşluk:** `staging.coremusic.net` / `test.coremusic.net` gibi **ayrı bir domain tanımı
diskte YOK** → staging'in hangi alt-alanda yaşayacağı **ADR bekliyor** (§7/3).

---

## §3 Ortam → Environment eşleme matrisi (taslak — GitHub ayarı diskte değil)

| Ortam | GitHub environment | Koruma kuralı | Secret kapsamı | Approver |
|---|---|---|---|---|
| local | — | yok (yerel) | `.env` yerel, gitignore | — |
| development | `development` | yok (her push) | dev-only | — |
| staging | `staging` | deployment branch: `main` | staging secret'ları | — |
| production | `production` | **required reviewer** + ön koşul: staging green | prod secret'ları | Vault Steward |

> **Not:** GitHub environment ayarları **repo arayüzündedir, diskte değildir** → tablo bir
> **taslak**tır ve `.github/**` yazımı beyaz-liste dışındadır. Uygulama DevOps onayı ister
> (`cd/index.md` §6 taslağı ile birlikte).

---

## §4 Environment koruma kuralları (taslak — GitHub resmi davranış)

```yaml
# TASLAK — uygulanmadı (environments GitHub ayarı diskte değil)
# Kaynak: docs.github.com/en/actions/deployment/targeting-different-environments/
#         using-environments-for-deployment (2026-10-17)
# Job içi kullanım:
jobs:
  deploy-production:
    environment: production     # ← GitHub bu ortamın koruma kurallarını uygular
```

**Uygulanabilir korumalar (GitHub arayüzü — diskte değil):**

1. **Required reviewers** — `deploy-production` job'u, atanmış kişi onaylayana kadar
   **bekler** (otomasyon durmaz; workflow run'ı "waiting" durumunda kalır).
2. **Deployment branch policy** — yalnız `main` (veya tag) bu ortama dağıtabilir.
3. **Wait timer** — onaydan sonra ek bekleme (kötü niyetli/yanlış onaya karşı sönümleyici).
4. **Environment secrets** — `PROD_*` secret'ları yalnız bu ortamda görünür; staging job'ı
   onlara **erişemez** (env-scope sızıntı önleme).

**Neden kritik:** "tam otomatik CD" kararı (`cd/index.md` §4) ile `deployment.md:260`
"İnsan onayı olmadan üretime dağıtma" yasağı arasındaki uzlaşma **bu 4. maddedir**:
otomasyon çalışır, insan son dokunuşu environment korumasıyla verir.

---

## §5 Secret ↔ environment kapsam matrisi (disk + taslak)

| Kapsam | Ne içerir | Erişen ortam | Disk kanıtı |
|---|---|---|---|
| Repo secret | — (hiçbiri repoda tanımlı görünmüyor) | tüm job'lar | `⚠️ VERIFICATION REQUIRED` (GitHub arayüzü) |
| Environment secret | `PROD_*` / `STAGING_*` | yalnız ilgili environment job | ADR bekliyor |
| `.gitleaks.toml` allowlist | yalnız test fixture şifreleri (ADR-034/095) | hepsi (gitignore ile) | `.gitleaks.toml:11-18` (gerçek) |
| `.gitignore` secret katmanı | `.env` ailesi + `config/.env` + `.ai/.env.*` | yerel | `.gitignore:5-9 · 69-71` |

**Kural:** ortam secret'ları `.md`/`.json`/`.log`'a yazılmaz (kök AGENTS.md §5 Security).
Allowlist'e üretim kodu girmez (`.gitleaks.toml:3-4` kuralı).

---

## §6 Çok-Dilli — environment doğrulama örnekleri (TASLAK)

### §6.1 Bash — ortam değişken doğrulama (CI öncesi fail-fast)

```bash
#!/usr/bin/env bash
# TASLAK — environments/index.md §3: deploy öncesi env tamamlığı
set -euo pipefail
ENV="${1:?kullanım: ./env-check.sh <staging|production>}"

# Gerekli değişkenler (üretim için zorunlu)
required=(DB_HOST DB_NAME DB_USER APP_ENV BASE_URL)
missing=()
for v in "${required[@]}"; do
  [ -n "${!v:-}" ] || missing+=("$v")
done

if [ ${#missing[@]} -gt 0 ]; then
  echo "[env-check] EKSİK (${ENV}): ${missing[*]}" >&2; exit 1
fi

# Ortam tutarlılığı: APP_ENV ortam adıyla eşleşmeli (yanlış ortamda yanlış secret önlenir)
[ "$APP_ENV" = "$ENV" ] || { echo "[env-check] APP_ENV=$APP_ENV ≠ $ENV" >&2; exit 1; }
echo "[env-check] ${ENV} OK (${#required[@]} değişken)"
```

### §6.2 PowerShell — Windows yerel env denetimi

```powershell
# TASLAK — environments/index.md §5: secret dosyası var mı, git'e giriyor mu?
$ErrorActionPreference = 'Stop'

# 1) .env git tarafından izleniyor mu? (izleniyorsa secret sızıntısı riski)
$tracked = git ls-files --error-unmatch .env 2>&1
if ($LASTEXITCODE -eq 0) { throw '.env GIT TARAFINDAN İZLENİYOR — derhal gitignore' }

# 2) kritik dosyalar ignore listesinde mi?
$gi = Get-Content .gitignore -Raw
foreach ($p in @('.env', 'config/.env', '.ai/.env.figma', 'shared/config/jwt/private.pem')) {
    if ($gi -notmatch [regex]::Escape($p)) { throw "$p .gitignore'da YOK" }
}

Write-Host '[env-check] secret dışlama katmanları OK' -ForegroundColor Green
```

### §6.3 PHP — ortam yapılandırması doğrulama (EnvParser benzeri)

```php
<?php
declare(strict_types=1);
// TASLAK — environments/index.md §3: runtime'da ortam tutarlılığı
// Çekirdek kural: üretim verisi yalnız production'da; yanlış ortamda üretim host'una bağlanma.

final class EnvironmentGuard
{
    private const ALLOWED = ['local', 'development', 'staging', 'production'];

    public function __construct(private readonly string $env) {}

    public function assertValid(): void
    {
        if (!in_array($this->env, self::ALLOWED, true)) {
            throw new \RuntimeException("Bilinmeyen ortam: {$this->env}");
        }
    }

    /** Üretim verisi yalnız production'da (KVKK + .gitleaks.toml ruhu) */
    public function assertNoProdData(): void
    {
        if ($this->env !== 'production' && getenv('DB_HOST') === 'prod-db.coremusic.net') {
            throw new \RuntimeException("Üretim DB host'u non-prod ortamda: {$this->env}");
        }
    }

    /** Non-prod: canlı kullanıcıya e-posta/bildirim YASAK */
    public function assertNoLiveMessaging(): void
    {
        if ($this->env !== 'production' && getenv('MAILER_TRANSPORT') === 'smtp-live') {
            throw new \RuntimeException('non-prod ortamda canlı mail transportu yasak');
        }
    }
}
```

### §6.4 JavaScript — ortam bayrağı doğrulama (E2E yapılandırması)

```javascript
// TASLAK — environments/index.md §3: E2E testi hangi ortama bağlanıyor?
const VALID = ['local', 'development', 'staging', 'production'];
const env = process.env.APP_ENV ?? 'local';

if (!VALID.includes(env)) {
  console.error(`[env] bilinmeyen ortam: ${env}`);
  process.exit(1);
}

// non-prod'da gerçek kullanıcı verisi/aksiyonu yasak
if (env !== 'production' && process.env.LIVE_USER_ACTION === '1') {
  console.error('[env] non-prod ortamda canlı aksiyon yasak');
  process.exit(1);
}

console.log(`[env] ${env} geçerli · baz ${process.env.BASE_URL}`);
```

---

## §7 Çelişki ve Karar Kayıtları (append-only — düzeltilmedi)

| # | Çelişki | Kaynak A | Kaynak B | Karar / durum |
|---|---|---|---|---|
| 1 | **3 vs 4 ortam** | `deployment.md:167-171` (Geliştirme/Test/Üretim) | Kullanıcı kararı 2026-10-17: `local·development·staging·production` | **Genişleme — ADR + deployment.md revizyonu bekliyor.** Metin silinmedi |
| 2 | **İsim uyuşmazlığı: Test vs staging** | deployment.md "Test" | kullanıcı "staging" | Aynı rol; `deployment.md` revizyonunda hizalanacak (ADR 1 ile birlikte) |
| 3 | **staging domaini diskte YOK** | config/domain.php 7 subdomain (staging/test yok) | staging ortamı kararı | **ADR bekliyor:** staging alt-alan adı |
| 4 | **`environment:` workflow'da yok** | grep 0 isabet | §3 taslak | Taslak (`cd/index.md` §6) — `.yml` beyaz-liste dışı |
| 5 | **CI env doğrulama adımı yok** | `ci.yml` 6 job'ında env-check step'i yok | §5 kapsam matrisi | Taslak (`infrastructure-testing/index.md` §5) |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `cd/index.md` §6 | deploy job'ında `environment:` kullanımı |
| `infrastructure-testing/index.md` §5 | env değişken eşleşme doğrulaması (CI gate) |
| `release-management/index.md` | ortam → release yayılım sırası |
| `rollback/index.md` | ortam bazlı geri alma (production'ın geri alma maliyeti en yüksek) |
| `secret-yonetimi.md` (deployment/) | secret ağacı (509s) — bu dosyada tekrarlanmadı |
| `.workflows/deployment.md:167-171` | bugünkü 3 ortam (§2.1) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17) · **Yasaklar:** `git commit` YOK · `*.yml` YOK (yalnız TASLAK) · `deployment.md` YOK
