---
node_id: "K007-data-quality-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-quality-web-arastirma"
evidence:
  - "satır D-Q1…D-Q6 — 2026-10-15 kaydı"
validation: "2026-10-15 — 6 iddia: 4 T4, 2 UNKNOWN"
open_questions:
  - "Coverage yüzdesi (K019 eşiği) → UNKNOWN"
group: kanit
---

# data-quality — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-Q1 | Repository testleri in-memory SQLite ile koşuyor | ApiKeyRepositoryTest.php:6-9,23-30 | — | T4 | ✅ DOĞRULANDI |
| D-Q2 | ENUM geçerliliği dokümante (user/service/server) | ApiKeyRepository::KEY_TYPES | — | T4 | ✅ DOĞRULANDI |
| D-Q3 | JSON şema doğrulama (JSON_VALID) | ApiKeyRepository docblock | — | T4 | ✅ DOĞRULANDI |
| D-Q4 | SELECT * yasak + parametreli sorgu | ADR-002 · .ai/.sql/CLAUDE.md §1 | php.net prepared-statements | T4 | ✅ DOĞRULANDI |
| D-Q5 | Yaygın doğruluk eşiği (accuracy target) | YOK | YOK | — | **UNKNOWN** |
| D-Q6 | Great Expectations / dbt tests benimsenmesi | repo'da YOK | greatexpectations.io | — | **UNKNOWN (K-05)** |

> D-Q6: ürün bağlamında hata toleransı sıfıra yakın zorunluluktur (master §19);
> çerçeve kararı yine de kanıtlanmadan iddia edilmez.

---

**Authority:** Bayram Ali / Vault Steward