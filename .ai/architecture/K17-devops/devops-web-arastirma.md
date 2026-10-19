---
title: "K17-devops — Web Araştırması"
node_id: K17-devops/devops-web-arastirma.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  - K18-testing
evidence:
  - "https://docs.github.com/actions — doğrulama 2026-10-18 (bu oturum)"
  - "https://github.com/gitleaks/gitleaks — doğrulama 2026-10-18 (bu oturum)"
  - "https://semver.org/ — doğrulama 2026-10-18 (bu oturum)"
  - "https://keepachangelog.com/en/1.1.0/ — doğrulama 2026-10-18 (bu oturum)"
  - "https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-branches-protecting-branches — doğrulama 2026-10-13 (ai-dogrulama.md §4 havuzu)"
validation: "2026-10-18 — 5 URL üretim öncesi doğrulandı; tarihler kaynak bazında dürüst"
open_questions: "⚠️ GitHub Actions/branch protection önerilerinin CoreMusic'e uygulanması ADR/onay bekliyor · ⚠️ branch-protection URL 2026-10-13 tarihli"
group: kanit
---

# K17-devops — Web Araştırması

## §1 ELI15

Bu dosya DevOps kararlarını **dış kaynaklara bağlar**: GitHub Actions (pipeline
belgeleri), GitLeaks (sır tarayıcısı), SemVer (sürümleme), Keep a Changelog
(not biçimi), branch protection (dal koruma). Kural: her URL üretimden **önce**
açılır ve **erişim tarihi** taşır; açılmayan madde yazılmaz.

## §2 Kapsam

- K17 kararlarına (D-7…D-21, §G.2 ADR'leri) **aday/kanıt** kaynaklar.
- Doğrulama: 2026-10-18 (4 kaynak, bu oturum) · 2026-10-13 (1 kaynak, havuz).

## §3 Doğrulanmış Kaynaklar

| # | Kaynak | URL | Erişim | Ne verir |
|---|--------|-----|--------|----------|
| S-1 | GitHub Actions — Docs | https://docs.github.com/actions | 2026-10-18 | Workflow kavramları: jobs/steps/needs/permissions/concurrency |
| S-2 | GitLeaks | https://github.com/gitleaks/gitleaks | 2026-10-18 | Tarama aracı: config öncelik sırası, allowlist, tam geçmiş |
| S-3 | Semantic Versioning 2.0.0 | https://semver.org/ | 2026-10-18 | Sürüm biçimi MAJOR.MINOR.PATCH + pre-release/meta |
| S-4 | Keep a Changelog 1.1.0 | https://keepachangelog.com/en/1.1.0/ | 2026-10-18 | Kategoriler: Added/Changed/Deprecated/Removed/Fixed/Security |
| S-5 | GitHub — Branch protection | https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-branches-protecting-branches | 2026-10-13 | Dal koruma: zorunlu review/status checks |

> **Doğrulama notu:** S-1…S-4 üretim öncesi bu oturumda açıldı (2026-10-18).
> S-5 havuzdan (2026-10-13) — yeniden okunmadı (⚠️ §6 X-01).

## §4 Çıkarımlar → Kararlar

### 4.1 Workflow yapı taşları → D-11…D-18
- S-1: `jobs` + `needs` (bağımlılık), `permissions` (en az yetki),
  `concurrency` (eşzamanlılık iptali), `continue-on-error` (yumuşak iş).
- **Çakışan kanıt:** Bizdeki karşılıkları diskte: `ci.yml:68` (needs) · `:14`
  (permissions) · `:17` (concurrency) · `:167/:183` (continue-on-error) —
  kaynakla **aynı kavramlar**, farklı dosya.
- **Çıkarım:** Soft gate'ler (D-12/13) GitHub'ın desteklediği meşru araçtır;
  sorun **kalıcılık**tır → geri dönüş kararı (R-02) ADR'ye yazılır.

### 4.2 Sır taraması → D-19…D-21
- S-2: tam geçmiş taraması için checkout `fetch-depth: 0`; config öncelik sırası
  (`--config` > `GITLEAKS_CONFIG` > `.gitleaks.toml`); allowlist
  `allowlist.paths/regexes`.
- **Çıkarım:** Bizde config kök `.gitleaks.toml` (D-20, config:2 yorumu) +
  `fetch-depth: 0` (D-19, secret-scan.yml:26) — kaynakla **tutarlı**.
  Allowlist sınırı (D-21) projeye özgü kural (ADR-034), aracın varsayılanı değil.

### 4.3 Sürümleme → release (§G.2/6 adayı)
- S-3: SemVer 2.0.0 — `MAJOR.MINOR.PATCH`; pre-release `-rc.1`, build `+meta`.
- **Çıkarım:** Rollback etiketleri (D-23) + artefakt adları (kod-ornekleri §9/§11)
  bu biçime uyar; release stratejisi kararı (§G.2/6 trunk-based önerisi) SemVer
  ile birlikte kapanır.

### 4.4 Sürüm notu biçimi → release
- S-4: kategoriler Added/Changed/Deprecated/Removed/Fixed/Security.
- **Çıkarım:** CHANGELOG doğrulayıcı iskeleti (kod-ornekleri §8) bu kapatılı
  listeyi kullanır; bilinmeyen kategori = hata.

### 4.5 Dal koruma → CI'ın bağlayıcılığı (R-01/R-08)
- S-5: zorunlu status checks + review → "CI geçmeden merge" kuralı platform
  düzeyinde kilitlenir.
- **Çıkarım:** R-01'in (CI geçti mi doğrulanmadı) asıl azaltması burasıdır:
  geçmeyen job merge'i engeller → "yeşil" iddiası platform kanıtı olur.
- **Durum:** CoreMusic'te branch protection **ayarlı mı** bilinmiyor (⚠️ §6
  X-02) — config kanıtı yok.

## §5 ADR'lerle Bağ (§G.2)

| §G.2 | İlgili kaynak | Not |
|------|--------------|-----|
| 1 (CD modeli) | S-1 | Actions'ta manuel approval/environment gate mümkün |
| 2 (artefakt) | S-1 · S-3 | artefakt + SemVer etiketi |
| 6 (release) | S-3 · S-4 | SemVer + changelog |
| 8 (index sayım) | — | vault işi, dış kaynak yok |

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| X-01 | S-5 içeriğinin 2026-10-18 durumu | 2026-10-13 tarihli, yeniden okunmadı |
| X-02 | CoreMusic'te branch protection açık mı | config kanıtı YOK |
| X-03 | Actions sürüm/artifact kullanımı (S-1 kolu) | §G.2/2 ADR bekliyor |
| X-04 | Karşılaştırmalı ikinci tur (ör. alternatif tarayıcılar) | yapılmadı |

## §6.1 Kaynak Kullanım Kuralları

| # | Kaynak | Bağlandığı karar | Dayanım |
|---|--------|------------------|---------|
| S-1 | GitHub Actions Docs | D-11…D-18 (yapı taşları) | platform resmi dokümanı |
| S-2 | GitLeaks | D-19…D-21 (tarama) | aracın kendi dokümanı |
| S-3 | SemVer 2.0.0 | release/artefakt (§G.2/2·6) | endüstri standardı |
| S-4 | Keep a Changelog 1.1.0 | changelog biçimi | endüstri standardı |
| S-5 | Branch protection | R-01 azaltması | platform resmi dokümanı |

1. **Platform dokümanı (S-1/S-5):** Comportment (davranış) dayanağıdır; CoreMusic
   konfigürasyonu **disk kanıtıyla** (ci.yml satırları) gösterilir, kaynakla
   karıştırılmaz.
2. **Araç dokümanı (S-2):** Aracın öncelik/allowlist davranışı → D-19/D-20
   dayanağı; **proje kuralı** (ADR-034) kaynaktan değil, `.gitleaks.toml`'dan gelir.
3. **Endüstri standardı (S-3/S-4):** release kararına (§G.2/6) giren biçimsel
   dayanak — ADR metninde "kaynak + erişim tarihi" ile anılır.

## §6.2 Tazelik ve Yeniden Doğrulama

- S-5 2026-10-13 tarihli → branch protection ayarı (X-02) doğrulanırken yeniden
  okunur (§6 X-01).
- S-1 platform güncellemesi sık → `needs/permissions/concurrency` davranışı
  ADR'ye girerken son doküman sürümü kontrol edilir.
- Erişilemeyen kaynak listede kalır, içeriği aktarılmaz (uydurma alıntı yok).

## §7 İlgili Dosyalar

- `devops-kararlar.md` — D-7…D-21 · §G.2 bekleyen ADR'ler
- `devops-riskler.md` — R-01/R-02/R-03
- `devops-kod-ornekleri.md` — §8/§9/§11 iskeletleri (S-3/S-4 karşılığı)
- `../../K009-security/` — tarama kapsamı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
