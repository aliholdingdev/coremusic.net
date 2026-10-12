---
node_id: K020-recomenataion/personalization
node_type: subdomain
status: draft
parent_id: K020-recomenataion
children: []
depends_on:
  - K020-recomenataion
  - ranking
evidence:
  - "v2 §2.20 — K020-recomenataion/ personalization/ (alt dosya listesi yok → children: [])"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Kişisel profil hangi veriden tutulacak, KVKK kapsamı ne? → VERIFY REQUIRED (K020-comilance açık sorusu)"
---

# K020-recomenataion/personalization — Kişiselleştirme Alt Bölgesi

**ELI15:** "Sıralamayı **bu kullanıcıya özel** nasıl hâle getiririz?" sorusunun alanı.

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` |
| Sorumluluk | Kullanıcıya göre **uyarlama** (profil → sıralama girdisi) |
| Bağımlılık | `ranking` (uyarlama, sıralamaya girer) |
| Profil verisi | **tanımlı değil** → `⚠️ VERIFY REQUIRED` |
| Gizlilik sınırı | KVKK/GDPR kapsamı tanımsız → `⚠️ VERIFY REQUIRED` |
| Varsayılan davranış | profil yoksa **kişiselleştirme yok** (fail-safe) — süreç kuralı |

**Kapı:** veri kaynağı ve gizlilik kapsamı onaylanmadan profil şeması yazılmaz.

**Authority:** Bayram Ali / Vault Steward
