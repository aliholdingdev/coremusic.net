---
node_id: "K007-indexing-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/indexing-web-arastirma"
evidence:
  - "satır D-I1…D-I5 — 2026-10-15 kaydı"
validation: "2026-10-15 — 5 iddia: 3 T4, 1 T3, 1 UNKNOWN"
open_questions:
  - "Çalışan sorguların EXPLAIN planı → ölçüm yok"
group: kanit
---

# indexing — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-I1 | Prefix indeks uzun varchar'da kullanılıyor | `idx_artist_credit_display(display_name(100))` | — | T4 | ✅ DOĞRULANDI |
| D-I2 | Kompozit UNIQUE sıra tekilliği | `uk_acn_credit_pos` | — | T4 | ✅ DOĞRULANDI |
| D-I3 | Düşük kardinalite kolon indeksli | `idx_album_types_sort_order` | — | T4 | ✅ DOĞRULANDI |
| D-I4 | Tablo yüklemeden sonra indeks eklemek daha hızlı | — | dev.mysql.com/en/column-indexes.html · §10.5.7 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-I5 | Fullscan sayısı / EXPLAIN rutini | YOK | — | — | **UNKNOWN** |

---

**Authority:** Bayram Ali / Vault Steward