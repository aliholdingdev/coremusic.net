---
node_id: K001-donanım/donanaım-type
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children:
  - hdd
  - audio
  - ssd
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı (yazım kaynağa sadık: donanaım-type)"
  - "donanım-anayasa.md §3 — 3 alt bağlam"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Envanter eksik: ekran, kablo, güç kaynağı bu ağaçta yok (K001 Q4)"
---

# K001-donanım/donanaım-type — ELI15

Donanımın **türlerine** göre ayrıldığı raf: içinde hangi parçaların olduğu değil,
hangi sınıftan parçaların bulunduğunu tarayan düğüm. Üç alt bağlam açıldı:
hdd · audio · ssd.

> yazım notu: donanaım-type yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.

> ⚠️ VERIFICATION REQUIRED: v2 bu düğümü yalnız adıyla tanımlar; envanter
> (ekran/kablo/güç kaynağı) bilinmiyor — UNKNOWN olarak bırakıldı.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K001-donanım |
| children | hdd · audio · ssd (bounded-context) |
| status | draft |
| Kapsam | Envanter tamamlanacak — K001 Q4 |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12