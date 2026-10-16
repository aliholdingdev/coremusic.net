---
node_id: "K007-synchronization-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/synchronization-web-arastirma"
evidence:
  - "satır D-Y1…D-Y4 — 2026-10-15 kaydı"
validation: "2026-10-15 — 4 iddia: 1 T4 (yokluk kanıtı), 2 T1, 1 UNKNOWN"
open_questions:
  - "S-3 sınır ADR'si bekleniyor"
group: kanit
---

# synchronization — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-Y1 | CDC/outbox altyapısı repo'da YOK | tarama: composer/src'ta Debezium/outbox referansı yok | — | T4 (yokluk) | ✅ DOĞRULANDI |
| D-Y2 | Outbox kalıbı yayın-sonrası teslim sağlar | — | microservices.io · debezium.io | T1 | ⚠️ yalnız atıf |
| D-Y3 | Saga dağıtık transaction için aday | — | microservices.io/patterns/data/saga.html | T1 | ⚠️ yalnız atıf |
| D-Y4 | CoreMusic hangi senkron kalıbını seçecek | YOK | — | — | **UNKNOWN** (K-07) |

---

**Authority:** Bayram Ali / Vault Steward