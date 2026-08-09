---
description: "Gorevleri analiz et, dogru komutu/skill'i oner, handover zincirini olustur"
agent: master-orchestrator
---

# Orchestrate Komutu

Gorevleri analiz eder, dogru yonlendirmeyi saglar.

## Nasil Calisir?

1. Gorevi analiz et (anahtar kelimeler)
2. Domain belirle (Backend, Frontend, Security, Data, QA, DevOps, Vault)
3. Vault context topla (max 15 dosya — MSA limit)
4. Komut oner
5. Handover zinciri olustur (cross-domain ise)

## Kullanim

```
/orchestrate [gorev aciklamasi]
```

## Domain Mapping

| Domain | Keyword | Komut |
|--------|---------|-------|
| Backend | API, endpoint, PHP, controller | /new-feature |
| Frontend | CSS, UI, responsive, ITCSS | /new-feature |
| Security | CSRF, CSP, OWASP, auth | /security-audit |
| Data | database, SQL, BCNF, MySQL | /db-normalize |
| QA | test, PHPUnit, Vitest | /test-run |
| DevOps | CI/CD, Docker, deploy | /deploy-check |
| Vault | vault, ADR, wiki-link | /vault-sync |

## Handover Ornekleri

```
Yeni API: /new-feature -> /security-audit -> /test-run
Yeni UI: /new-feature (UI) -> /new-feature (API) -> /test-run
Guvenlik: /security-audit -> /new-feature -> /test-run -> /vault-sync
DB: /db-normalize -> /new-feature -> /security-audit -> /test-run
```

## Red Team Kontrolu

- Dosya yollari dogru mu?
- ADR celiskisi var mi?
- MSA limiti (15 dosya) asildi mi?
- Layer violation var mi?
