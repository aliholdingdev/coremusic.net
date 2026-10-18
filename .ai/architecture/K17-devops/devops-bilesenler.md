---
title: "K17-devops — Bileşenler"
node_id: K17-devops/devops-bilesenler.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - ".github/workflows/ci.yml:24 (jobs) · iş adları 26/67/111/143/161/179 · 2026-10-18"
  - ".github/workflows/secret-scan.yml:21 (runs-on) · 2026-10-18"
  - ".gitleaks.toml:1-18 (config + allowlist, ADR-034/ADR-095) · 2026-10-18"
  - ".workflows/deployment.md (deployment playbook) · 2026-10-18"
  - ".ai/architecture/K17-devops/devops-master.md §B (30 karar) · §E (repo kanıtları) · 2026-10-18"
validation: "2026-10-18 — job adları/ADR referansları grep+dosya okuma ile doğrulandı"
open_questions: "⚠️ CI geçti mi doğrulanmadı · ⚠️ CD modeli ADR bekliyor (deployment.md insan-onayı ↔ tam otomatik CD) · ⚠️ staging alt-alan YOK · ⚠️ 3↔4 ortam kararı ADR bekliyor"
group: mimari
---

# K17-devops — Bileşenler

## §1 ELI15

Bu dosya kodun **ekipman (CI/CD) parçalarını** anlatır. Depo'ya kod itildiğinde
otomatik çalışan makineler var: `ci.yml` altı iş (lint, test, denetim, JS testi,
kapsama, e2e), `secret-scan.yml` sızıntı tarayıcısı, `.gitleaks.toml` onun kural
dosyası ve `deployment.md` yayına alma kılavuzu. Her parçanın ne yaptığı, nerede
durduğu ve kime güvendiği aşağıda; çalışıp çalışmadığı (CI geçti mi) ayrı bir
soru — o **doğrulanmadı** (⚠️).

## §2 Kapsam

- **Kapsamda:** `.github/workflows/` (ci.yml, secret-scan.yml) · `.gitleaks.toml` ·
  `.workflows/deployment.md` + master §B kararlarının bileşen eşlemesi.
- **Kapsamda değil:** Test altyapısının kendisi (K18-testing), güvenlik duvarının
  geneli (K009-security).

## §3 Bileşen Envanteri

| # | Bileşen | Dosya | Sorumluluk | Kanıt |
|---|---------|-------|-----------|-------|
| C-01 | CI Pipeline | `.github/workflows/ci.yml` | 6 işlik otomatik kapı | `:24` jobs · `:26/67/111/143/161/179` |
| C-01a | PHP Lint | ci.yml (iş) | PHPStan statik analiz | `:26` · `:62` (PHPStan) |
| C-01b | PHP Tests | ci.yml (iş) | PHPUnit (matrix) | `:67` · `:104` |
| C-01c | Composer Audit | ci.yml (iş) | bağımlılık güvenlik denetimi | `:111` · `:123/129/135` |
| C-01d | JS Unit Tests | ci.yml (iş) | Vitest | `:143` · `:157` |
| C-01e | JS Coverage | ci.yml (iş) | Kapsama gate (soft, A7) | `:161` · `:175` |
| C-01f | JS E2E | ci.yml (iş) | Playwright (soft, A6) | `:179` · `:202` |
| C-02 | Secret Scan | `.github/workflows/secret-scan.yml` | Sır taraması (gitleaks-action) | `:21` |
| C-03 | Gitleaks Config | `.gitleaks.toml` | Tarama kuralı + allowlist | `:1-18` |
| C-04 | Deployment Playbook | `.workflows/deployment.md` | Yayına alma kılavuzu (insan onayı) | §260 bağlamı (master §E) |

## §4 C-01 CI Pipeline — 6 İş

| İş | Ad (ci.yml) | Ne yapar | Yumuşak mı? |
|----|-------------|----------|--------------|
| 1 | PHP Lint (`:26`) | composer install ×3 + PHPStan (`:62`) | hayır (gate) |
| 2 | PHP Tests (`:67`) | matrix: proje başına PHPUnit (`:104`) | hayır |
| 3 | Composer Audit (`:111`) | bağımlılık CVE denetimi (3 proje) | hayır |
| 4 | JS Unit Tests (`:143`) | npm ci + Vitest (`:154/:157`) | hayır |
| 5 | JS Coverage (`:161`) | Vitest coverage — **soft, A7** | **evet** |
| 6 | JS E2E (`:179`) | Playwright chromium (`:199/:202`) — **soft, A6** | **evet** |

- **Ortak altyapı:** `permissions:` (`:14`) · `concurrency:` (`:17`) · `env:` (`:21`)
  — pipeline'ın güvenlik ve tekillik ayarları.
- **Yumuşak kapı notu:** 5. ve 6. iş "soft" — başarısızlık pipeline'ı kırmıyor
  (master §B kararı ile ilişkili; ayrıntı `devops-kararlar.md`).

## §5 C-02 Secret Scan

- **Sorumluluk:** Depoda sır sızıntısı taraması; ubuntu üzerinde
  (`secret-scan.yml:21`).
- **Yapı:** `gitleaks-action@v2` kökteki `.gitleaks.toml`'u otomatik kullanır
  (config `:2` yorumu).
- **Kesişim:** K009-security (depends_on) — sır taraması güvenlik kapısının
  CI ayağıdır.

## §6 C-03 Gitleaks Config (`.gitleaks.toml`, 18 satır)

- **`useDefault = true`** (`:8-9`) — varsayılan kural seti + eklenti.
- **Allowlist** (`:11-18`) **yalnız** test dosyalarını kapsar:
  - `auth.coremusic.net/tests/.../LoginRequestTest.php` (`:14`) — sahte şifreler.
  - `shared/tests/Fixtures/jwt/test2?-private.pem` (`:17`) — JWT test fixture
    (ADR-095); üretim private.pem `.gitignore`'da (`:16`).
- **Kural (ADR-034 §5.2 madde 5 + §4.1-R6, `:3-4`):** üretimi/ortamı kapsayan
  allowlist **yasak**; bulgu = PR engeli. → Gerçek ADR numaraları diskte
  gömülüdür (uydurma yok).

## §7 C-04 Deployment Playbook (`.workflows/deployment.md`)

- **Sorumluluk:** Yayına alma adımları, ortamlar, geri alma.
- **Açık karar:** İnsani onaylı adım (deployment.md ~260 bağlamı) ↔ tam otomatik
  CD çatışması **ADR bekliyor** (master §G.2 — `devops-kararlar.md` D-CD-2).
- **Boşluk:** staging alt-alan **YOK** (master §G) → ortam sayısı (3↔4) kararı
  beklemede.

## §8 Bağımlılık Haritası (bileşen düzeyi)

```
push/PR ──► ci.yml (6 iş)
              ├── PHP Lint / PHP Tests / Composer Audit  ──► K18-testing
              ├── JS Unit / Coverage(soft) / E2E(soft)
              └── (gate: branch koruma — K009 ile kesişim)
push/PR ──► secret-scan.yml ──► .gitleaks.toml (ADR-034/095) ──► K009-security
release  ──► .workflows/deployment.md (insan onayı — CD modeli ADR bekliyor)
```

## §9 Master Uyum (`devops-master.md`)

- §B: 30 karar → `devops-kararlar.md`.
- §E: repo kanıtları (bu dosyadaki satırlar).
- §G.1: **11 çelişki** → `devops-riskler.md` §5 (envanter).
- §G.2: **8 bekleyen ADR** → `devops-kararlar.md` §5.
- Ölçüm bölümü (§G) → sayımlar bu pakette bayat olabilir: `index.md:462` bayat
  sayım **onay bekliyor** (⚠️).

## §10 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| U-01 | CI'ın son çalışması geçti mi | doğrulanmadı — "CI yeşil" denmez |
| U-02 | CD modeli (onay vs otomatik) | ADR bekliyor |
| U-03 | staging alt-alan | YOK (master §G) |
| U-04 | Ortam sayısı 3↔4 | ADR bekliyor |
| U-05 | `index.md:462` bayat sayım | onay bekliyor |
| U-06 | A6/A7 soft gate'lerin yumuşatma nedeni/geri dönüşü | master §B'de kayıt, süre belirsiz |

## §11 İlgili Dosyalar

- `devops-master.md` §B/§E/§F/§G — karar, kanıt, web, ölçüm
- `devops-kararlar.md` · `devops-riskler.md` · `devops-test-stratejisi.md`
- `../K009-security/` · `../K18-testing/`
- `.github/workflows/ci.yml` · `.github/workflows/secret-scan.yml` · `.gitleaks.toml`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
