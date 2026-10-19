---
title: "K17-devops — Kararlar"
node_id: K17-devops/devops-kararlar.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - ".ai/architecture/K17-devops/devops-master.md §B.1 karar defteri (30 kayıt, satır 219-252) · 2026-10-18"
  - ".ai/architecture/K17-devops/devops-master.md §G.1 (11 çelişki) · §G.2 (8 bekleyen ADR) · 2026-10-18"
  - ".gitleaks.toml:3-4 (ADR-034) · :15 (ADR-095) · 2026-10-18"
  - ".ai/.decisions/index.md (ADR envanteri — uydurma yasak) · 2026-10-18"
validation: "2026-10-18 — 30 karar başlığı/ dayanağı master §B.1'den birebir; ADR numaraları yalnız diskten"
open_questions: "⚠️ 8 bekleyen ADR numara ataması bekliyor · ⚠️ karar 29 (depo durumu) çelişki kayıtlı · ⚠️ index.md:462 sayım onayı bekliyor"
group: mimari
---

# K17-devops — Kararlar

## §1 ELI15

Bu dosya DevOps kararlarının **defteridir**: pipeline'ı kim sahiplenir, sır
taraması nasıl çalışır, deploy neden insan onayı ister. 30 karar disk kanıtıyla
(master §B.1) gelir; ADR numaraları **yalnız diskten** yazılır. Henüz numara
almamış 8 konu "ADR numarası atanacak" olarak bekler — numara uydurmak yasaktır.

## §2 Kapsam

- K17-devops kararları; master `devops-master.md` §B.1'deki 30 maddenin bu
  paketteki okunabilir kaydı.
- Çelişkiler §5'te (master §G.1 ile aynı gerçeklik), bekleyenler §6'da (§G.2).

## §3 Kararlar — Sahiplik & Süreç

| # | Karar | Dayanak (master §B.1) | Durum |
|---|-------|------------------------|-------|
| 1 | CI testleri koşar, tasarımını K019 yapar | v2 §2.18/§2.19 · ci.yml php-test | kanıtlı |
| 2 | Uyarı K017, aksiyon K018 | v2 §2.17/§2.18 | kanıtlı |
| 3 | ci.yml sahibi DevOps + QA (test kapsamı) | `.github/AGENTS.md` satır 59 | kanıtlı |
| 4 | secret-scan.yml sahibi Security (kapsam) + DevOps (yml) | `.github/AGENTS.md` satır 60 | kanıtlı |
| 5 | Yeni `.yml` taslak → kullanıcı onayı → uygulama | `.github/AGENTS.md` satır 63 | kanıtlı |
| 6 | Subagent git commit atmaz | `.github/AGENTS.md` satır 85 | kanıtlı |
| 30 | Bu revizyon commit'i atılmaz | `.github/AGENTS.md` satır 85 | uygulandı |

> **K-6/K-30 notu:** Bu dosyanın yazarı da subagenttir — yazım sonunda commit
  **atılmaz** (yetki orkestratörde).

## §4 Kararlar — Pipeline Mekaniği (ci.yml)

| # | Karar | Dayanak | Durum |
|---|-------|---------|-------|
| 7 | PHP sürümü composer.json'dan türetilir (8.4) | ci.yml:22 · shared/composer.json:8 | kanıtlı |
| 8 | Statik analiz composer script'inden koşar (level=5) | ci.yml:62-63 · composer.json:48 | kanıtlı |
| 9 | `phpstan.neon` yok; script esas | ci.yml:60-61 yorumu | kanıtlı |
| 10 | auth testleri yalnız Unit koşar (Integration dizini diskte yok) | ci.yml:77-80 | kanıtlı |
| 11 | php-test, php-lint'e bağımlı (`needs`) | ci.yml:68 | kanıtlı |
| 12 | js-coverage **soft gate** (A7) | ci.yml:167 `continue-on-error` | kanıtlı |
| 13 | js-e2e **soft gate** (A6) | ci.yml:183 `continue-on-error` | kanıtlı |
| 14 | Node 26 toolchain (vitest/jsdom engine aralığı) | ci.yml:151 | kanıtlı |
| 15 | Composer cache her job'da ayrı key | ci.yml:44 · 96 | kanıtlı |
| 16 | npm ci kilitli kurulum | ci.yml:155 · 174 · 197 | kanıtlı |
| 25 | JS test komutları package.json scripts'inden | package.json:5·7·8 | kanıtlı |
| 26 | 3 composer install hedefi: shared · auth · home | ci.yml:48·52·56 | kanıtlı |
| 27 | composer audit 3 hedefte koşulur | ci.yml:126·132·138 | kanıtlı |
| 24 | Şablon: github-actions-template.md §3.2 | ci.yml:2 · secret-scan.yml:2 · şablon:30 | kanıtlı |

## §5 Kararlar — Güvenlik & Git

| # | Karar | Dayanak | Durum |
|---|-------|---------|-------|
| 17 | Workflow `permissions` en az yetkide | ci.yml:14-15 · secret-scan.yml:11-12 | kanıtlı |
| 18 | Aynı ref'te eşzamanlı çalışma iptal edilir (`concurrency`) | ci.yml:17-19 · secret-scan.yml:14-16 | kanıtlı |
| 19 | GitLeaks **tam geçmiş** taranır (fetch-depth 0) | secret-scan.yml:26 | kanıtlı |
| 20 | GitLeaks config'i kök `.gitleaks.toml`'dan okunur | `.gitleaks.toml:2` | kanıtlı (2026-10-13) |
| 21 | Allowlist yalnız test dosyasındaki sahte şifreleri kapsar | `.gitleaks.toml:3-4` (**ADR-034** §5.2/§4.1-R6) | kanıtlı |
| 28 | `k13-cicd` plan referansı diskte YOK | ci.yml:3 · Test-Path = False | **kanıtlı negatif** |

> **Gerçek ADR numaraları (diskten):** **ADR-034** (gitleaks allowlist,
> `.gitleaks.toml:3`) · **ADR-095** (JWT test fixture keypair, `.gitleaks.toml:15`).
> Bunlar uydurma değil, config yorumlarında gömülü numaralardır.

## §6 Kararlar — Deploy & Ortam

| # | Karar | Dayanak | Durum |
|---|-------|---------|-------|
| 22 | Deploy onayı `.workflows/deployment.md` **8 aşamada** yürür | deployment.md:126-199 | kanıtlı (2026-10-13) |
| 23 | Rollback prosedürü deployment.md §4'te | deployment.md:212 | kanıtlı (2026-10-13) |
| 29 | Depo durumu: main dalı VAR (son commit c46f08ac 2026-10-12) | `.git/HEAD:1` · git log | **çelişki** (§7 #1) |

## §7 Çelişki Kayıtları (master §G.1 — disk kazanır)

| # | Çelişki | Sonuç |
|---|---------|-------|
| 1 | "depo YOK" ↔ `.git/HEAD` VAR | DÜZELTİLDİ (§A.2/7) |
| 2 | secret-scan.yml "config YOK" ↔ `.gitleaks.toml` VAR (18 satır) | gövde korundu |
| 3 | deployment.md:260 insan-onayı ↔ tam otomatik CD önerisi | **ADR bekliyor** (§8/1) |
| 4 | 3 ortam ↔ 4 ortam (local·development·staging·production) | **ADR bekliyor** (§8/4) |
| 5 | `.github/AGENTS.md:191` "3 job" ↔ ci.yml **6 job** (25·66·110·142·160·178) | doküman bayat |
| 6 | v2 "10 çocuk" ↔ build/ YOK | KAPANDI (build/index.md 218 satır) |
| 7 | "10 alt dizinde index" ↔ index YOK | KAPANDI (pipeline/index.md 291) |
| 8 | K018-devops/K019-testing vb. ↔ disk K17/K18/K16/K19 | DÜZELTİLDİ (2026-10-17, 191 ref) |
| 9 | index.md:462 "17 · 2.819" ↔ diskte 23 · 8.877 | **AÇIK** — beyaz-liste dışı, onay bekliyor |
| 10 | "doğrulanmadı" ↔ deployment.md VAR (281 satır) | KAPANDI (§A.2/9) |
| 11 | "19 dosya · 3.866" ↔ 2026-10-17: 23 dosya · 8.877 | FM güncellendi (§G.3) |

## §8 Bekleyen ADR (master §G.2 — 8 kayıt; **numaralar atanacak**)

| # | ADR konusu | Kaynak |
|---|-----------|--------|
| 1 | CD modeli uzlaşımı (insan-onayı ↔ tam otomatik) | cd/index.md open_questions:22 |
| 2 | Artefakt modeli (git-snapshot / artifact-registry / imaj) | cd/index.md §5:225 |
| 3 | Staging alt-alan adı (config/domain.php 7 subdomain, staging YOK) | environments/index.md:19·76·249 |
| 4 | Ortam 3→4 genişlemesi | environments/index.md §2-3 |
| 5 | Environment secret ayrımı (`PROD_*`/`STAGING_*`, `environment:` 0 isabet) | environments/index.md:127 |
| 6 | Release stratejisi son seçimi (3 strateji → trunk-based önerisi) | release-management/index.md:21 |
| 7 | Docker/Terraform test kapsamı (0 isabet) | infrastructure-testing/index.md:17·291 |
| 8 | index.md:462 sayım tazelenmesi (kullanıcı onayı) | .ai/architecture/index.md |

> **Yukarıdaki 8 satırın hiçbiri numara taşımaz** — hepsi **"ADR numarası
> atanacak"**. `.ai/.decisions/index.md` bu oturumda yalnız ADR-083/087/088'i
> "NO FILE on disk" olarak döndürdü; uydurma numara yazılmadı.

## §9 Karar ↔ Bileşen Eşlemesi

| Karar grubu | Bileşen |
|-------------|---------|
| 7-16, 24-27 | C-01 ci.yml (6 iş) |
| 17-21, 28 | C-02 secret-scan.yml · C-03 .gitleaks.toml |
| 22-23 (+ §8/1-7) | C-04 deployment.md |
| 1-6, 29-30 | sahiplik/süreç (Vault + `.github/AGENTS.md`) |

## §10 İlgili Dosyalar

- `devops-master.md` §B.1 — kaynak defter (30 satır) · §G — çelişki/ADR/ölçüm
- `devops-bilesenler.md` — bileşen eşlemesi
- `devops-riskler.md` — karar riskleri
- `../../.decisions/index.md` — gerçek ADR numaralarının tek kaynağı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
