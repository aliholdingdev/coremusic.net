---
node_id: "K007-repositories-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories-web-arastirma"
evidence:
  - "satır D-R1…D-R6 — 2026-10-15 kaydı"
validation: "2026-10-15 — 6 iddia: 4 T4, 2 UNKNOWN"
open_questions:
  - "Repository dışı doğrudan sorgu var mı → tarama yapılmadı"
group: kanit
---

# repositories — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-R1 | ORM yasak, PDO prepared-only | ADR-002 · ApiKeyRepository docblock | php.net pdo.prepared-statements.php | T4 | ✅ DOĞRULANDI |
| D-R2 | SELECT * yasak; kolon listesi açık | `SELECT_COLUMNS` sabiti | — | T4 | ✅ DOĞRULANDI |
| D-R3 | Ham anahtar asla DB'ye yazılmaz | ApiKeyRepository.php:18-22 docblock | — | T4 | ✅ DOĞRULANDI |
| D-R4 | EMULATE_PREPARES=false → gerçek prepared | DatabaseManager.php:22-27 | php.net ATTR_EMULATE_PREPARES | T4 | ✅ DOĞRULANDI |
| D-R5 | Tüm tablolar repository üzerinden erişiliyor | yalnız 1 repository var | — | — | **UNKNOWN** |
| D-R6 | N+1 koruması | YOK | YOK | — | **UNKNOWN** |

---

**Authority:** Bayram Ali / Vault Steward