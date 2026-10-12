---
node_id: K020-recomenataion/ranking
node_type: subdomain
status: draft
parent_id: K020-recomenataion
children: []
depends_on:
  - K020-recomenataion
evidence:
  - "v2 §2.20 — K020-recomenataion/ ranking/ (alt dosya listesi yok → children: [])"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Sıralama yöntemi (kural tabanlı / skor / model) seçilmedi → VERIFY REQUIRED"
---

# K020-recomenataion/ranking — Sıralama Alt Bölgesi

**ELI15:** "Adaylardan **hangisi üstte** olsun?" kararının verildiği yer.

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` (v2'de alt liste yok) |
| Sorumluluk | Aday kümesini **sıralamak** (sıra = çıktı) |
| Girdi | `K020-recomenataion` aday havuzu (bkz. `[[../recommendation-master]]` §3) |
| Çıktı | sıralı liste — `evaluation/`ün ölçeceği şey |
| Yöntem | **seçilmedi** → `⚠️ VERIFY REQUIRED` |
| Eşik/skor | **tanımlı değil** — sayı uydurulmaz |

**Kapı:** yöntem seçimi ADR gerektirir (`../K020-adr` üzerinden).

**Authority:** Bayram Ali / Vault Steward
