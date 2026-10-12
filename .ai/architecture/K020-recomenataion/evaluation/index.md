---
node_id: K020-recomenataion/evaluation
node_type: subdomain
status: draft
parent_id: K020-recomenataion
children: []
depends_on:
  - K020-recomenataion
  - ranking
  - personalization
evidence:
  - "v2 §2.20 — K020-recomenataion/ evaluation/ (alt dosya listesi yok → children: [])"
  - "Araç seçimi: web/araç araştırması YAPILMADI → iddia yok"
validation:
  - "2026-10-12 — şekil v2 §2.20 ile birebir; içerik yalnız iskelet"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Hangi metrik, hangi eşik, hangi veri seti? Tanımlanmadı → VERIFY REQUIRED (uydurma metrik/eşik YASAK)"
---

# K020-recomenataion/evaluation — Öneri Değerlendirmesi

**ELI15:** "Öneri **gerçekten işe yaradı mı?**" sorusunun cevaplandığı yer.
Ölçüm tanımlanmadan öneri **yayına alınamaz** (süreç kuralı).

| Alan | Değer |
|---|---|
| `node_type` | `subdomain` · `children: []` |
| Sorumluluk | Öneri çıktısını **ölçmek** ve kaydetmek |
| Girdi | `ranking` sırası + kullanıcı davranışı (dinlenme/atlama) |
| Metrikler | **tanımlanmadı** → `⚠️ VERIFY REQUIRED` (örnek isimler bile doğrulanmadan yazılmaz) |
| Eşik (geç/kal) | **yok** → `⚠️ VERIFY REQUIRED` |
| Sonuç kaydı | `verification` mantığıyla append-only; tur kayıtları `K020-comilance/verification/` ile uyumlu |

**Kapı:** metrik + eşik + veri tanımsızsa `status: verify-required` kalır.

**Authority:** Bayram Ali / Vault Steward
