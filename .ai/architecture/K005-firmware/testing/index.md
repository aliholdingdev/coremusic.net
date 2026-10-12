---
node_id: K005-firmware/testing
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children: []
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §3 satır 9 · D-kriterleri (§6)"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️"
---

# K005-firmware/testing — ELI15

Firmware'in doğru çalıştığını kanıtlayan senaryolar: açılış testi, komut yankısı, güncelleme/geri alma provası, güç kesintisi dayanıklılığı.

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.5'teki **adından**
> türetilmiştir; v2 çocukları içerik olarak tanımlamaz. Register/sürüm/protokol
> bilgileri kanıt turunda netleşecektir — uydurma değer yazılmaz.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K005-firmware |
| children | yok (yaprak) |
| status | draft |
| Kapsam | Tanım bekliyor — kanıt turu-2 |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
