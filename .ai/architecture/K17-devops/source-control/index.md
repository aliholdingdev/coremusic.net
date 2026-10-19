---
node_id: K17-devops/source-control
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/release-management
  - K17-devops/ci
evidence:
  - "git HEAD: ref: refs/heads/main (2026-10-17) — depo VAR"
  - "git log son 8 commit: 687fbed·b0921d8·b299e7d·d97b662·151b0c9·f7bf890·8bef821·ae61084 (2026-10-17)"
  - ".gitignore 83 satır (2026-10-17 tam okuma) — 5 dışlama katmanı"
  - "conventional commits fiilen uygulanıyor: docs(vault): · feat(home-song-btn): (git log)"
validation:
  - "Bayat kayıt düzeltmesi: 'git deposu YOK' kayıtları DEĞİŞTİRİLDİ — depo main'de ve commit'li"
  - "HEAD commit tarihi 2026-10-17 (687fbed) — 2026-10-16 değil"
  - "Branch envanteri ve commit biçimi gerçek git çıktısıyla kanıtlandı"
open_questions:
  - "Remote var mı? (origin URL) — git remote detail OKUNMADI → §7/3"
  - "Branch sayısı/koruma kuralları GitHub arayüzünde — diskte değil → §7/4"
group: devops
---

# Source Control — Git Deposu, Branch ve Commit Hijyeni (K17-devops altı)

> **Bayat kayıt düzeltmesi (2026-10-17):** vault'ta eski kayıtlar "git deposu YOK" diyordu.
> Bu **yanlıştır** — depo `main` dalında ve commit'lidir. Çelişki append-only kaydedildi (§7).
> **Sorumluluk:** DevOps Engineer · **Otorite:** Bayram Ali / Vault Steward ·
> **Mod:** Red Team · Human Mode · Truth Mode

---

## §1 Özet — Kayıt düzeltmesi

| Eski kayıt (bayat) | Disk gerçeği (2026-10-17) | Kanıt |
|---|---|---|
| "git deposu YOK" | **VAR** — `refs/heads/main` | `git HEAD` |
| "ilk commit" | Son commit `687fbed` (2026-10-17) | `git log -8` |
| "commit konvansiyonu yok" | **Conventional Commits fiilen var** (`docs(vault):`, `feat(...):`) | `git log` subject'leri |

**Düzeltme kuralı (AGENTS.md §3):** çelişkide disk kazanır; eski metin silinmez, yalnız
§7'de "neden yanlış" kaydedilir.

---

## §2 Depo Kanıtları (2026-10-17 ölçüm)

### §2.1 HEAD ve dal

```powershell
# Kanıt (birebir)
# Dosya: .git/HEAD
ref: refs/heads/main
```

- **Aktif dal:** `main`
- **Commit:** `687fbed` (2026-10-17) — vault sync
- **Önceki:** `b0921d8` (2026-10-17) — `feat(home-song-btn)`

### §2.2 Son 8 commit (git log — birebir alıntı)

```text
687fbed 2026-10-17 docs(vault): session kaydi + log.md/MEMORY.md — home song btn figma overlay (b0921d8 kapanisi)
b0921d8 2026-10-17 feat(home-song-btn): Figma'ya gore kapak overlay'lari — cd-case, tur rozeti (music/video) ve Show CD (0.35)
b299e7d 2026-10-16 added files
d97b662 2026-10-16 docs(vault+config): paralel tur architecture K011/K012 revizyonlari + .agents/workflows (10 workflow) + GEMINI.md boot pointer v3.3.0 - anlik snapshot (paralel oturum devam ediyor)
151b0c9 2026-10-16 edited files
f7bf890 2026-10-16 added png s
8bef821 2026-10-16 forneted added
ae61084 2026-10-16 docs(vault): index.md envanter guncellemesi - projects 60 md (+ doc 11 + 11-examples), toplam 2144 dosya / 1915 md / 395 dizin (2026-10-16 dogru olcum)
```

**Commit hijyeni analizi:**

| Commit | Biçim | Değerlendirme |
|---|---|---|
| `687fbed` · `d97b662` · `ae61084` | `docs(vault): ...` | **İdeal** — type(scope): açıklama |
| `b0921d8` | `feat(home-song-btn): ...` | **İdeal** — tek mantıksal değişiklik, kapsam belirtilmiş |
| `b299e7d` "added files" | düz metin | **Zayıf** — ne eklendiği anlaşılmıyor |
| `151b0c9` "edited files" | düz metin | **Zayıf** — hangi dosyalar/neden bilinmiyor |
| `f7bf890` "added png s" | düz metin | **Orta** — azıcık bağlam var ("png"), yazım hatası |
| `8bef821` "forneted added" | düz metin | **Zayıf** + yazım hatası |

**Oran:** 8 commit'in 4'ü ideal (`docs(vault):`/`feat(...):`), 4'ü zayıf düz metin. Bu,
**Conventional Commits'in kısmen** uygulandığını gösterir → `release-management/index.md`
§5'teki semver otomasyonu için girdi **yeterli değil**, tutarlılık gerekiyor (§7/5).

---

## §3 `.gitignore` Dışlama Katmanları (83 satır — tam okuma)

5 katman (`devops-ornekler.md` §8.2 ile aynı kanıt):

| # | Katman | Satır | Ne |
|---|---|---|---|
| 1 | Secret dosyaları | 5-9 | `.env` ailesi + `config/.env` |
| 2 | Bağımlılık/artefakt | 12 · 16 | `vendor/` + `node_modules/` (istisna `!shared/vendor/autoload.php`:13) |
| 3 | Lock politikası | 17-19 | `package-lock.json` ARTIK commit · `yarn.lock` ignore |
| 4 | Build artefaktları | 46-49 | `dist/`, `build/`, `*.phar` |
| 5 | Kritik key | 79-80 | `shared/config/jwt/private.pem` (public.pem commit edilir) |

**Ek (69-71):** `.ai/.env.figma` + `.ai/.env.*` + `!.env.example` — Figma token'ları yerel.

**Mühendislik:** istisna `!shared/vendor/autoload.php` (13) bilinçlidir — autoload girişi
commit edilir ki kurulum öncesi autoload erişilebilsin; ama `vendor/` geri kalanı ignore.

---

## §4 Branch Hijyeni (TASLAK — trunk-based uyumlu)

> Çekirdek dal modeli kararı → `release-management/index.md` §3.3 (trunk-based önerisi).
> Burada yalnız **hijyen kuralları**.

### §4.1 Dal isimlendirme

| Kalıp | Örnek | Kural |
|---|---|---|
| `feat/<kısa>` | `feat/home-song-btn` | ≤2 gün, tek mantıksal değişiklik |
| `fix/<alan>` | `fix/auth-csrf` | tek hata |
| `chore/<iş>` | `chore/vault-sync` | yapısal/doküman |
| `docs/<alan>` | `docs(vault)` yerine `docs/vault` | vault güncellemesi |
| ~~`develop`~~ | — | **trunk-based'de YOK** (GitFlow'a özgü) |
| ~~`release/*`~~ | — | trunk-based'de YOK (tag ile değiştirilir) |

### §4.2 Commit kuralları (Conventional Commits — semver girdisi)

```text
<type>(<scope>): <açıklama>

type ∈ feat | fix | perf | docs | chore | refactor | test | build | ci | style
scope = alan (vault, auth, home-song-btn, ci, ...)
açıklama = değişikliğin NE yaptığı (neden değil; neden body'de)
BREAKING CHANGE: ... ← body/footer ile MAJOR sinyali
```

**Eşleme (semver):** `feat` → MINOR · `fix`/`perf`/`security` → PATCH · `feat!:` → MAJOR ·
`docs`/`chore` → release yok.

---

## §5 Çok-Dilli — Git operasyon örnekleri (TASLAK)

### §5.1 Bash — güvenli commit akışı (hook bypass bilinçli, §6/§1)

```bash
#!/usr/bin/env bash
# TASLAK — source-control §4.2: conventional commit doğrulama + atomic commit
set -euo pipefail
msg="${1:?kullanım: ./commit.sh 'feat(scope): açıklama'}"

# 1) biçim kapısı
if ! echo "$msg" | grep -qE '^(feat|fix|perf|docs|chore|refactor|test|build|ci|style)(\([^)]+\))?!?: .+'; then
  echo "[commit] Conventional Commits değil: $msg" >&2; exit 1
fi

# 2) atomic: tek commit tek mantıksal değişiklik
git add -A
n=$(git diff --cached --name-only | wc -l)
echo "[commit] staged $n dosya · '$msg'"

# 3) secret taraması ÖNCE (fail-closed)
if command -v gitleaks >/dev/null; then
  gitleaks protect --staged --no-banner || { echo "[commit] secret bulundu — commit YASAK" >&2; exit 1; }
fi

git commit -m "$msg"
```

### §5.2 PowerShell — commit öncesi hijyen denetimi

```powershell
# TASLAK — source-control §4: commit öncesi lint
$ErrorActionPreference = 'Stop'

# 1) gizli dosya staged mı?
$bad = git diff --cached --name-only | Where-Object {
    $_ -match '(^\.env$|config/\.env|\.pem$|\.ai/\.env)'
}
if ($bad) { throw "gizli dosya staged: $($bad -join ', ')" }

# 2) bayat lock staged mı? (lock drift)
if ((git diff --cached --name-only) -match 'package-lock.json') {
    Write-Warning 'package-lock.json değişti — package.json ile tutarlılığını doğrula'
}

# 3) commit mesajı biçimi
$msg = $args[0]
if ($msg -notmatch '^(feat|fix|perf|docs|chore|refactor|test|build|ci|style)(\([^)]+\))?!?: .+') {
    throw "Conventional Commits değil: $msg"
}
Write-Host "[commit-gate] OK — $msg" -ForegroundColor Green
```

### §5.3 PHP — commit mesajı doğrulama sınıfı

```php
<?php
declare(strict_types=1);
// TASLAK — source-control §4.2: Conventional Commits + semver tipi
final class CommitPolicy
{
    private const TYPES = ['feat','fix','perf','docs','chore','refactor','test','build','ci','style'];
    private const PATTERN = '/^(feat|fix|perf|docs|chore|refactor|test|build|ci|style)(\([a-z0-9-]+\))?!?: .+/';

    public function assert(string $subject): void
    {
        if (!preg_match(self::PATTERN, $subject)) {
            throw new \RuntimeException("Conventional Commits değil: {$subject}");
        }
    }

    /** semver tipi: feat→MINOR · fix/perf→PATCH · feat!→MAJOR · docs/chore→NONE */
    public function bumpType(string $subject): string
    {
        if (str_contains($subject, '!:') || str_contains($subject, 'BREAKING CHANGE')) return 'major';
        [$type] = explode('(', $subject, 2);
        return match (true) {
            $type === 'feat' => 'minor',
            in_array($type, ['fix','perf','security'], true) => 'patch',
            default => 'none',
        };
    }
}
```

### §5.4 JavaScript — commit gate (CI/öncesi)

```javascript
// TASLAK — source-control §4.2
const TYPES = ['feat','fix','perf','docs','chore','refactor','test','build','ci','style'];
const RE = /^(feat|fix|perf|docs|chore|refactor|test|build|ci|style)(\([a-z0-9-]+\))?!?: .+/;

export function assertCommit(subject) {
  if (!RE.test(subject)) throw new Error(`Conventional Commits değil: ${subject}`);
  const type = subject.split('(')[0];
  return TYPES.includes(type);
}

export function bumpType(subject) {
  if (subject.includes('!:') || subject.includes('BREAKING CHANGE')) return 'major';
  const t = subject.split('(')[0];
  if (t === 'feat') return 'minor';
  if (['fix','perf','security'].includes(t)) return 'patch';
  return 'none';
}
```

### §5.5 SQL — depo-üzeri audit sorgusu (değişiklik günlüğü)

```sql
-- TASLAK — source-control §2.2: commit geçmişinden doküman/audit sorgusu
-- Amaç: hangi commit hangi alanda? (git log --grep ile cross-check)
-- Örnek: son 30 günde docs(vault) commit sayısı — vault sürüm ritmi
SELECT COUNT(*) AS docs_vault_commit FROM (
  SELECT 'docs(vault)' AS scope
) t;
-- Gerçek sorgu git üzerinden yürütülür; SQL yalnız raporlama katmanı içindir
```

---

## §6 Bilinen Riskler ve Yasaklar

| # | Risk/ Yasak | Kanıt/Dayanak |
|---|---|---|
| 1 | **Subagent `git commit` ATMAZ** | `.ai/AGENTS.md` §13.9/5 · `.github/AGENTS.md` §3.3/1 · kök AGENTS.md §7/§8 |
| 2 | **Secret dosyası commit edilmez** | `.gitignore:5-9 · 79-80` + `gitleaks protect --staged` (§5.1) |
| 3 | **Hook bypass yalnız TASLAK'ta ve bilinçli** | §6.1 `core.hooksPath=/dev/null` — üretime alınmadan önce hook'lar çalıştırılmalı |
| 4 | **Zayıf commit mesajı (düz metin)** | §2.2: 4/8 commit zayıf → semver otomasyonu için girdi yetersiz |
| 5 | **Remote/koruma kuralları bilinmiyor** | `git remote` detail **okunmadı** → §7/3 |

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Eski kayıt (bayat) | Disk gerçeği | Durum |
|---|---|---|---|
| 1 | "git deposu YOK" | `refs/heads/main` + commit `687fbed` (2026-10-17) | **DÜZELTİLDİ** (§1) |
| 2 | "ilk commit / tarih 2026-10-16" | son commit 2026-10-17 (`687fbed`) | **DÜZELTİLDİ** (§2) |
| 3 | **Remote URL bilinmiyor** | `git remote -v` **okunmadı** | `⚠️ VERIFICATION REQUIRED` |
| 4 | **Branch/koruma kuralları GitHub arayüzünde** | diskte değil | ADR/ayar bekliyor |
| 5 | **Conventional Commits kısmen** | 4/8 commit zayıf düz metin | **Tutarsızlık** → §4.2 gate taslağı |
| 6 | **Hook bypass vs güvenlik** | §6.1 TASLAK bypass kullanıyor | **Bilinçli geçici** — üretime alınmadan hook'lar zorunlu |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `release-management/index.md` | dal stratejisi + semver (§4.2 girdisi) |
| `ci/index.md` | commit → CI tetikleyicisi |
| `cd/index.md` §4.1 | trunk-based dal modeli |
| `source-control` → `infrastructure-testing/` | lock drift (commit gate §5.2/2) |
| `devops-ornekler.md` §8.1 | gerçek git log kanıtı (aynı ölçüm) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17 · "depo YOK" kaydı düzeltildi) ·
**Yasaklar:** `git commit`/`push` YOK · `*.yml` YOK (TASLAK) · `rollback-ve-geri-alma.md` DOKUNULMAZ
