---
title: "K17-devops — Test Stratejisi"
node_id: K17-devops/devops-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K17-devops/index.md
children: []
depends_on:
  - K009-security
  K18-testing
evidence:
  - ".github/workflows/ci.yml:24 (jobs) · :62/:68/:104/:157/:175/:202 (adımlar) · 2026-10-18"
  - ".github/workflows/secret-scan.yml:21/26 (tarama) · 2026-10-18"
  - ".workflows/deployment.md:126-199 (8 aşama) · :212 (rollback) · 2026-10-18"
  - ".ai/architecture/K17-devops/devops-master.md §G.2 (8 bekleyen ADR) · 2026-10-18"
validation: "2026-10-18 — piramit + senaryolar + kabul kriterleri; CI sonucu doğrulanmadı (UNKNOWN)"
open_questions: "⚠️ CI geçti mi doğrulanmadı · ⚠️ rollback tatbikatı yok · ⚠️ altyapı test kapsamı 0 isabet (§G.2/7 ADR bekliyor)"
group: mimari
---

# K17-devops — Test Stratejisi

## §1 ELI15

Bu dosya **pipeline'ın kendisinin nasıl sınamalandığını** anlatır — kodu değil,
**kapıları**: CI düzgün koşuyor mu, tarama gerçekten tarıyor mu, deploy onayı
atlanabilir mi, rollback çalışır mı? Piramit burada biraz farklıdır: en altta
pipeline'ın kendisi, en üstte kriz senaryoları (rollback tatbikatı).

## §2 Kapsam

- ci.yml işleri · secret-scan.yml · .gitleaks.toml · deployment.md aşamaları.
- Uygulama testleri K18-testing'in (depends_on); burada **kapı** testleri var.

## §3 Test Piramidi (DevOps)

```
        ┌──────────────────────────────┐
        │ Kriz (2-3)                   │  rollback tatbikatı, onaysız yayın denemesi
        ├──────────────────────────────┤
        │ Pipeline entegrasyon (6-10)  │  job sırası, needs, cache, matrix
        ├──────────────────────────────┤
        │ Kapı birim (10+)             │  config doğrulama, allowlist, syntax
        └──────────────────────────────┘
```

| Katman | Hedef | Not |
|--------|-------|-----|
| Kapı birimi | workflow syntax + config geçerli | actionlint/yamllint + `gitleaks detect --dry-run` |
| Pipeline entegrasyon | 6 job sırası + bağımlılıklar | gerçek CI çalışması (⚠️ §7) |
| Kriz | rollback + red | insan onaylı, tatbikat kanıtlı |

## §4 Kapı Birim Senaryoları

| # | Senaryo | Hedef (kanıt) | Kabul kriteri |
|---|---------|---------------|----------------|
| U-01 | ci.yml YAML sözdizimi | `ci.yml:24` jobs | actionlint/yamllint **0 hata** |
| U-02 | secret-scan.yml sözdizimi | `secret-scan.yml:21` | 0 hata |
| U-03 | .gitleaks.toml geçerli TOML | `.gitleaks.toml:1-18` | `gitleaks detect` config'i yükler |
| U-04 | allowlist yalnız test yolları | config:13-18 (ADR-034) | allowlist'te `/tests/` dışı path **YOK** |
| U-05 | `permissions` en az yetki | ci.yml:14 · secret-scan:11 | `write` yetkisi yok |
| U-06 | `concurrency` + iptal | ci.yml:17 · secret-scan:14 | tanımlı, `cancel-in-progress: true` |
| U-07 | `needs` sırası | ci.yml:68 | php-test → php-lint bağımlılığı yerinde |
| U-08 | soft gate işaretleri | ci.yml:167/183 | `continue-on-error: true` **görünür** (yanlışlıkla hard olmamış) |

## §5 Pipeline Entegrasyon Senaryoları (risk bağlantılı)

| # | Senaryo | Bağlı risk | Kabul kriteri |
|---|---------|-----------|----------------|
| I-01 | Tam CI koşusu (6 job) | R-01 | 4 zorunlu job geçer; 2 soft job **görünür** sonuç verir |
| I-02 | Kırık test ile PR | R-01/R-08 | php-test job'ı **kırmızı** → merge engellenir |
| I-03 | PHPStan hatası | R-08 | php-lint job'ı kırmızı → php-test `needs` ile beklemez/kırılır |
| I-04 | Sahte sır (üretim path'inde) | R-03 | gitleaks **bulur** → PR engeli |
| I-05 | Sahte sır (test fixture path'inde) | R-03 | allowlist ile geçer (config:13-18) |
| I-06 | Kapsama düşüşü | R-02 | soft job sarı/kırmızı **görünür** ama pipeline'ı kırmaz (D-12) |
| I-07 | Eşzamanlı 2 push (aynı ref) | R-08 | eskisi iptal (D-18) |
| I-08 | `environment:` tanımsız deploy | R-06 | §G.2/5 açık — davranış ADR'ye kadar **tanımsız** (⚠️) |

## §6 Kriz Senaryoları

| # | Senaryo | Kabul kriteri |
|---|---------|----------------|
| C-01 | Onaysız üretim yayını denemesi | `deployment.md:260` yasağı → **durdurulur** (çıkış ≠ 0) |
| C-02 | Rollback (deployment.md:212) | Son iyi etikete dönüş; **tatbikat kanıtı gerekli** (⚠️ §7 T-04) |
| C-03 | Yarım yayında sağlık yok | health fail → yayın başarısız sayılır (deploy kuralı, CD ADR bekliyor) |

## §7 Kablosuz Doğrulama / CI Durumu

- ⚠️ **CI son çalışması bu oturumda doğrulanmadı** → "CI yeşil" denemez
  (R-01 · U-01). Doğrulama: Actions → son run → job durumları (ekran/URL kanıtı).
- ⚠️ **T-04:** Rollback tatbikatı **yapılmadı/kanıt yok** → C-02 "hazır" sayılmaz.
- ⚠️ **T-05:** Altyapı test kapsamı (Dockerfile/compose/`*.tf`) **0 isabet** —
  §G.2/7 ADR bekliyor; bu pakette kapsam dışı bırakıldı, "testli" denmez.
- ⚠️ **T-06:** actionlint/yamllint gibi workflow linter'ının disk kanıtı **UNKNOWN**
  (envanterde geçmiyor) → U-01…U-03 henüz otomatik değil, elle doğrulama sayılır.

## §8 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | İlgili |
|---|-----------|--------|
| T-01 | CI son çalışması geçti mi | R-01 · §7 |
| T-02 | Workflow linter varlığı | §7 T-06 |
| T-03 | `environment:`/secret ayrımı davranışı | §5 I-08 · R-06 |
| T-04 | Rollback tatbikat kanıtı | §6 C-02 · R-10 |
| T-05 | Altyapı test kapsamı (Docker/Terraform) | §7 · §G.2/7 |

## §9 Ölçüm Eşikleri (kabul kriterleri)

| Metrik | Eşik | Kaynak |
|--------|------|--------|
| Zorunlu job geçişi (lint/test/audit/js-unit) | %100 | ci.yml |
| gitleaks bulgusu (üretim path'i) | 0 | secret-scan.yml |
| Soft job'lar | görünür sonuç + soft kalmaya devam (D-12/13) | ci.yml |
| Workflow sözdizimi hatası | 0 | U-01/U-02 |
| DevOps standart başarım (hedef) | ≥%95 | `.ai/AGENTS.md` §16 |

## §10 İlgili Dosyalar

- `devops-riskler.md` — R-01…R-11
- `devops-bilesenler.md` — C-01…C-04
- `devops-kararlar.md` §7-§8 — çelişki + bekleyen ADR
- `../K18-testing/` — uygulama testleri · `../K009-security/` — tarama kapsamı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
