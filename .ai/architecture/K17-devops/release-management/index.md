---
node_id: K17-devops/release-management
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/source-control
  - K17-devops/cd
evidence:
  - "git tag envanteri: git tag --list = 0 adet (2026-10-17)"
  - "git log son 8 commit (2026-10-17) — conventional commits biçimi fiilen uygulanıyor"
  - "package.json: 'version' alanı YOK (18 satır, 2026-10-17)"
  - "shared/composer.json: 'version': '2.0.0' (2026-10-17)"
  - "trunk-based vs GitFlow web araştırması: 2026-10-17 (§6 karar matrisi kaynakları)"
validation:
  - "3 strateji de (kullanıcı kararı 2026-10-17) — matriste; CoreMusic önerisi ayrı satırda"
  - "semver + tag örnekleri: git tag --list ile mevcut tag olmadığını ölçtük (0 adet)"
  - "release/trigger ci.yml'de YOK (grep 0 isabet)"
open_questions:
  - "Hangi strateji seçilecek? — kullanıcı 3'ünü de matriste istedi; son seçim ADR bekliyor"
  - "Semver'in CoreMusic'e uygulanabilirliği: package.json version alanı yok, composer version 2.0.0 — tutarlılık ADR"
group: devops
---

# Release Management — Sürümleme ve Strateji Karar Matrisi (K17-devops altı)

> **Karar (kullanıcı, 2026-10-17): 3 strateji de karar matrisinde** olsun; numaralı seçim
> adımları verilsin; CoreMusic önerisi ayrı satırda. **Sorumluluk:** DevOps Engineer ·
> **Otorite:** Bayram Ali / Vault Steward · **Mod:** Red Team · Human Mode · Truth Mode

---

## §1 Özet — Bugün sürümleme var mı?

| Sinyal | Ölçüm (2026-10-17) | Yorum |
|---|---|---|
| `git tag --list` | **0 adet** | Hiç etiketli sürüm yok |
| GitHub Release trigger (`tags:`/`release:`) | **0 isabet** | CI tetikleyicisi yalnız `push:/pull_request:` |
| `package.json` `version` alanı | **YOK** | JS paketi semver taşımıyor |
| `shared/composer.json` `version` | `2.0.0` | PHP paketi sürüm taşıyor |
| Commit biçimi | `docs(vault):` · `feat(...):` | **Conventional Commits fiilen var** |

**Sonuç:** sürümleme **henüz başlamamış**; ama commit konvansiyonu (Conventional Commits)
çalıştığından semver'e geçiş için ham madde mevcut. Release = `git tag` + `deployment/`
aşamalarıyla birleşecek.

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|---|---|
| Strateji seçimi (GitFlow/GitHub Flow/trunk) | Branch hijyeni ayrıntısı → `source-control/` |
| Semver + tag + release kaydı | Deploy mekaniği → `cd/index.md` |
| Release ↔ CD ilişkisi | Geri alma süreç → `rollback/` |
| Sürüm geçmişi/CHANGELOG | Artefakt üretimi → `build/` |

**Kaçınılan tekrar:** `deployment/yayilim-stratejileri.md` (502s) yayılım *taktikleri* için
(kanarya/blue-green vb. — onu okumadan iddia edilmez); bu dosya **dal/sürüm stratejisi**
içindir. İkisi farklı katmandır.

---

## §3 Karar Matrisi — 3 Strateji (§6 kaynaklı)

### §3.1 Boyut tanımları

- **Dal yapısı:** kaç kalıcı dal var?
- **Sürüm ritmi:** ne sıklıkla release çıkar?
- **Rollback maliyeti:** geri alma ne kadar pahalı?
- **Ekipleşme:** kaç kişi/ajan paralel çalışıyor?
- **CI yükü:** her push'ta koşar mı?

### §3.2 Matris (§6 web kaynakları — 2026-10-17)

| Boyut | **A. GitHub Flow** (tek dal + PR) | **B. GitFlow** (main + develop + release/ + feature/) | **C. Trunk-based** (tek dal, kısa ömürlü branch) |
|---|---|---|---|
| Dal yapısı | `main` + feature (PR) | `main` · `develop` · `release/*` · `feature/*` · `hotfix/*` | `main` + ≤2 günlük branch |
| Sürüm ritmi | isteğe bağlı | sürümlü (release kolu) | sürekli (her green main = aday) |
| Release karmaşıklığı | düşük | **yüksek** (release kolu merge + tag) | düşük |
| Rollback maliyeti | orta | düşük (sürüme dön) | orta (önceki imaj/tag) |
| Paralel ajan ekibi | iyi | iyi | **en iyi** (kısa branch, sık merge) |
| CI yükü | push + PR | develop/release kolları ayrı CI | her push (concurrency ile) |
| Uygun proje | küçük-orta ekip | sürümlü/kutulan ürün | sürekli dağıtan SaaS |
| Öğrenme eğrisi | düşük | **yüksek** | orta |

### §3.3 CoreMusic için ÖNERİ (ayrı satır — §5)

> **Öneri: C — Trunk-based + tam otomatik CD**, `main` tek dal, Conventional Commits semver
> girdisi, environment korumalı staging → prod.
>
> **Gerekçe (disk kanıtı):**
> 1. `package.json` sürüm alanı yok, `git tag` 0 → sürüm ritmi henüz kurulmamış; GitFlow'un
>    `develop`/`release` kollarını kurmak **sıfırdan süreç maliyeti** olurdu.
> 2. Commit biçimi fiilen Conventional Commits (`docs(vault):`, `feat(...):`) → semver
>    otomasyonu için girdi zaten var.
> 3. Kullanıcının **tam otomatik CD** seçimi (2026-10-17) GitFlow'un release kolu ritmiyle
>    değil, trunk-based'in sürekli akışıyla uyumlu.
> 4. Tek-kullanıcı + ajan çalışma biçimi: uzun ömürlü `develop` kolu, ajanların paralel
>    çalışmasını değil senkron tutar.
>
> **Çelişki:** bu öneri `deployment.md:260` insan-onayı yasağıyla kısmen gerilir →
> `cd/index.md` §7/1 ve §6 `environment: production` çözümüne bakın.

---

## §4 Numaralı Seçim Adımları (1-2-3-4)

| Adım | Soru | Cevap → yön | Doğrulama |
|---|---|---|---|
| **1** | Ürün sürekli mi yayında? (SaaS/web vs sürümlü masaüstü) | sürekli → **C** (trunk) · sürümlü → **B** (GitFlow) | `config/domain.php` 7 canlı subdomain → sürekli yanıt |
| **2** | Kaç kişi/ajan aynı anda `main`'e yazıyor? | ≥2 paralel → C veya B · tek → **A** | ajan çokluğu (vault/registry) → C/B |
| **3** | Release ritmi ne? (haftalık/aylık/günlük) | günlük → C · aylık sürümlü → B | `git log` sıklığı (§8) → günlük commit var |
| **4** | Rollback ne kadar pahalı olabilir? (veri migration'lı mı) | pahalı → geri alma planı **zorunlu** (`rollback/`) | `deployment.md:257-263` geri dönüş planı zorunlu |

**Sonuç:** 1=C · 2=C/B · 3=C · 4 zorunlu → **C (trunk-based)** önerisi desteklenir; nihai
seçim ADR ile onaylanır.

---

## §5 Semver + Tag Uygulaması (TASLAK — `git tag` üretim amacı, `git push` yasak)

### §5.1 Semver kuralı

| Parça | Ne değişince | CoreMusic karşılığı |
|---|---|---|
| **MAJOR** (`v2.0.0`) | geriye uyumsuz API/DB/rota | route + DTO + migration kırılması |
| **MINOR** (`v2.1.0`) | uyumlu yeni özellik | yeni endpoint/rota/özelliik |
| **PATCH** (`v2.1.1`) | uyumlu düzeltme | bugfix, güvenlik yaması |

**Conventional Commits → semver eşlemesi:**
`feat:` → MINOR · `fix:` / `perf:` / `security:` → PATCH · `feat!:` / `BREAKING CHANGE:` → MAJOR · `docs:`/`chore:` → release yok.

### §5.2 Tag üretimi (Bash — CI'da çalıştırılacak TASLAK)

```bash
#!/usr/bin/env bash
# TASLAK — release-management §5: conventional commit'ten semver tag'i
set -euo pipefail
last=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
IFS=. read -r maj min pat <<< "${last#v}"; pat=${pat%%-*}

# Son tag'den bu yana commit'leri tara
if git log "$(git describe --tags --abbrev=0 2>/dev/null || git rev-list --max-parents=0 HEAD)..HEAD" \
    --pretty=%s | grep -qE '^[a-z]+(\([^)]*\))?!:|BREAKING CHANGE:'; then
  maj=$((maj+1)); min=0; pat=0
elif git log "$(git describe --tags --abbrev=0 2>/dev/null || git rev-list --max-parents=0 HEAD)..HEAD" \
    --pretty=%s | grep -qE '^feat(\(|:)'; then
  min=$((min+1)); pat=0
else
  pat=$((pat+1))
fi
echo "v${maj}.${min}.${pat}"
# Not: git tag -a / git push --tags YALNIZ orkestratör/CI yetkisindedir (AGENTS.md §7/§8)
```

### §5.3 Tag üretimi (PowerShell)

```powershell
# TASLAK — release-management §5
$ErrorActionPreference = 'Stop'
$last = (git describe --tags --abbrev=0 2>$null) ?? 'v0.0.0'
if (-not $last) { $last = 'v0.0.0' }
$v = $last.TrimStart('v').Split('.')
$major, $minor, $patch = [int]$v[0], [int]$v[1], [int]$v[2]

$subjects = git log "$last..HEAD" --pretty=%s
if ($subjects -match '^[a-z]+(\([^)]*\))?!:' -or $subjects -match 'BREAKING CHANGE:') {
    $major++; $minor = 0; $patch = 0
} elseif ($subjects -match '^feat(\(|:)') {
    $minor++; $patch = 0
} else {
    $patch++
}
"v$major.$minor.$patch"
```

### §5.4 Semver doğrulama (PHP)

```php
<?php
declare(strict_types=1);
// TASLAK — release-management §5.1: tag biçimi + Conventional Commit doğrulama
final class ReleaseGate
{
    private const SEMVER = '/^v(\d+)\.(\d+)\.(\d+)(-[0-9A-Za-z.-]+)?(\+[0-9A-Za-z.-]+)?$/';
    private const COMMIT = '/^(feat|fix|perf|docs|chore|refactor|test|build|ci|style)(\([a-z0-9-]+\))?!?:/i';

    public function assertTag(string $tag): void
    {
        if (!preg_match(self::SEMVER, $tag)) {
            throw new \RuntimeException("Geçersiz semver tag: {$tag}");
        }
    }

    /** Conventional Commit değilse release kapağı açılmaz */
    public function assertCommit(string $subject): void
    {
        if (!preg_match(self::COMMIT, $subject)) {
            throw new \RuntimeException("Conventional Commit değil: {$subject}");
        }
    }
}
```

---

## §6 Karar Matrisi Kaynakları (web — 2026-10-17 erişildi)

| İddia | Kaynak | Erişim |
|---|---|---|
| Trunk-based development prensipleri (kısa branch, sık merge, sürekli deploy) | trunkbased.com | 2026-10-17 |
| GitFlow'un sürüm-odaklı yapısı (main/develop/release/hotfix kolları) | Vincent Driessen — nvie.com/posts/a-successful-git-branching-model | 2026-10-17 |
| GitHub Flow: tek dal + PR + deploy odaklı dokümantasyon | docs.github.com/en/get-started/using-github/github-flow | 2026-10-17 |
| Deployment environment korumaları (release stratejisiyle birlikte) | docs.github.com/en/actions/deployment/.../using-environments-for-deployment | 2026-10-17 |

> **Kural (AGENTS.md §3 Research):** mimari karar → 10+ kaynak, cross-check zorunlu.
> Bu matris 4 birincil + §5 sentinel (semver.org) kaynakla **başlangıç** seviyesindedir;
> nihai ADR öncesi `researcher` + `research-analyst` ile genişletilmelidir → §7/4.

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Konu | Kaynak A | Kaynak B | Durum |
|---|---|---|---|---|
| 1 | **Hiç `git tag` yok** | `git tag --list` = 0 | release-management hedefi | **Başlangıç boşluğu** — §5 taslağı |
| 2 | **`package.json` version yok vs `composer 2.0.0`** | package.json (18s, alan yok) | shared/composer.json:6 `2.0.0` | **Tutarlılık ADR** — JS tarafı semver taşımıyor |
| 3 | **Release trigger yok** | ci.yml `on:` yalnız push/pull_request | tag tabanlı release | → `cd/index.md` §6 taslağı |
| 4 | **Matris kaynak sayısı yetersiz** | §6 yalnız 4 birincil kaynak | AGENTS.md §5 Research 10+ | **ADR öncesi genişletme** gerekiyor |
| 5 | **Yayılım taktiği ayrı dosya** | `deployment/yayilim-stratejileri.md` (502s) | bu dosya dal stratejisi | **Bilinçli ayrım** — tekrar yok |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `source-control/index.md` | seçilen stratejinin dal/hijyen tarafı |
| `cd/index.md` §4 | trunk-based CD ile birlikte çalışır |
| `release-management` → `build/` | release adayı artefakt üretimi |
| `deployment/yayilim-stratejileri.md` | yayılım *taktikleri* (502s) — bu dosyanın tamamlayıcısı |
| `rollback/index.md` | sürüm geri alma (tag → önceki sürüme dönüş) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17) · **Yasaklar:** `git commit`/`git tag` push YOK · `*.yml` YOK (TASLAK) · `deployment/yayilim-stratejileri.md` düzenlenmedi
