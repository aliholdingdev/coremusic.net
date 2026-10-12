---
node_id: K001-donanım/donanım-api
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children:
[]
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı"
  - "donanım-anayasa.md §3 çocuk satırı"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "API yüzeyi hangi protokole oturuyor (sürücü ioctl / sysfs benzeri)? ⚠️"
---

# K001-donanım/donanım-api — ELI15

Donanımın yazılıma açılan arayüzü: üst katmanların (K000 sürücü, K003 ses motoru) parça bilgisine sormasına izin veren çağrı seti. Donanımın kendisi değil, onun dışa kapalı olmayan yüzü.

> ⚠️ VERIFICATION REQUIRED: Bu dugumun tanimi yalnizca v2 §2.2'deki **adindan**
> turetilmistir; v2 cocuklari icerik olarak tanimlamaz. Gorev, arayuz ve
> kabul kriterleri kanit turunda netlesecek.

## Durum

| Alan | Deger |
|---|---|
| node_type | subdomain |
| parent | K001-donanım |
| children | yok (yaprak) |
| status | draft |
| Kapsam | Kanit turu-2 bekliyor |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12