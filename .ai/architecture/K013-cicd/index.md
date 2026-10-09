---
type: architecture
category: layer
title: "K013 — CI/CD"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K013 — CI/CD

## §1 Kimlik
- Katman: K013 · Alan: **A4** (K12-K15).
- Kapsam: CI/CD — GitHub Actions, secret taraması, kalite kapıları, teslim.

## §2 Sorumluluk
1. `ci.yml` ana pipeline (build/test denetimi).
2. `secret-scan.yml` + `.gitleaks.toml` (GitLeaks) sızıntı taraması.
3. Vault gate'leri: LINT/şablon/ölçüm script'leri repo KÖKÜNDEN çalışır (AGENTS §13.6).
4. Deploy/rollback ve ortam matrisi (devops-engineer).
5. Modül sınırı denetiminin CI'a taşınması (plan §6 Faz 1 — mimari onay kapısı).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K000 (runner/altyapı).
- **Üst:** cross-cutting — K001–K020 tüm katmanların teslimi (bağımlılık yalnız alt zemine).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-082 | Dev/Staging Environment Architecture |

CI'ya özel ADR: **YOK** (`.ai/.decisions/index.md`'de pipeline başlıklı ADR bulunmadı) → **UNKNOWN**.

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `.github/workflows/{ci.yml,secret-scan.yml}` (2 dosya) · `.gitleaks.toml` kökte.

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: CI'nin geçtiği doğrulanamadı — GitHub Actions çalışma geçmişi diskte yok (AGENTS §25.2 aynı kaydı taşır).
- Workflow tetikleyicileri/izin matrisi okunmadı → **UNKNOWN**.
